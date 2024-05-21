<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PunchRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PunchRecordController extends Controller
{
    public function punchIn(Request $request, $id)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 401);
            }

            $latitudeTolerance = 0.0008;
            $longitudeTolerance = 0.0002;

            $existingLocation = Location::whereBetween('latitude', [$request->latitude - $latitudeTolerance, $request->latitude + $latitudeTolerance])
                ->whereBetween('longitude', [$request->longitude - $longitudeTolerance, $request->longitude + $longitudeTolerance])
                ->first();

            if ($existingLocation) {
                $locationId = $existingLocation->id;
            } else {

                $location = Location::create([
                    'name' => $this->getLocationName($request->latitude, $request->longitude),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
                $locationId = $location->id;
            }

            $punchRecord = PunchRecord::create([
                'user_id' => $id,
                'punch_in_location_id' => $locationId,
                'punch_in_time' => Carbon::now(),
            ]);

            $data = ['punch_record' => $punchRecord];

            return $this->returnJsonResponse(true, 'Punched in successfully', $data);
        } catch (\Exception $exception) {
            // Log the error and return failure response
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function punchOut(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 401);
            }

            $tolerance = 0.000001;
            $latestPunchRecord = PunchRecord::where('user_id', $id)
                ->whereNull('punch_out_time')
                ->latest()
                ->first();

            if (!$latestPunchRecord) {
                return $this->returnJsonResponse(false, 'No active punch in found', []);
            }

            $existingLocation = Location::whereBetween('latitude', [$request->latitude - $tolerance, $request->latitude + $tolerance])
                ->whereBetween('longitude', [$request->longitude - $tolerance, $request->longitude + $tolerance])
                ->first();

            if ($existingLocation) {
                $latestPunchRecord->update([
                    'punch_out_location_id' => $existingLocation->id,
                    'punch_out_time' => Carbon::now(),
                ]);
            } else {

                $location = Location::create([
                    'name' => $this->getLocationName($request->latitude, $request->longitude),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);

                $latestPunchRecord->update([
                    'punch_out_location_id' => $location->id,
                    'punch_out_time' => Carbon::now(),
                ]);
            }

            $data = ['punch_record' => $latestPunchRecord];

            return $this->returnJsonResponse(true, 'Punched out successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function getTodayPunchRecords($userId)
    {

        try {

            $startOfDay = Carbon::now()->startOfDay();
            $endOfDay = Carbon::now()->endOfDay();
            $punchRecords = PunchRecord::where('user_id', $userId)
                ->whereBetween('punch_in_time', [$startOfDay, $endOfDay])
                ->get();
            $data = ['todayPunchRecords' => $punchRecords];

            return $this->returnJsonResponse(true, 'To-days punch successfully', $data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }



    public function getPunchRecordsByDateRange(Request $request, $userId)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'startDate' => 'required|date',
                'endDate' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 401);
            }

            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();

            // Retrieve punch records within the date range
            $punchRecords = PunchRecord::where('user_id', $userId)
                ->whereBetween('punch_in_time', [$startDate, $endDate])
                ->orderBy('punch_in_time')
                ->get()
                ->groupBy(function($record) {
                    return Carbon::parse($record->punch_in_time)->format('Y-m-d');
                });

            $data = ['punchRecords' => $punchRecords];

            return $this->returnJsonResponse(true, 'Punch records retrieved successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


// public function getUserPreviousRecords($userId)
// {

// }

    public function getPunchRecordsByDateRangeUsers(Request $request)
{
    try {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 401);
        }

        $startDate_check = Carbon::parse($request->startDate)->startOfDay();
        $endDate_check = Carbon::parse($request->endDate)->endOfDay();

        if($startDate_check >$endDate_check)
        {
        $startDate=$endDate_check;
        $endDate=$startDate_check;
        }else{
            $startDate=$startDate_check;
            $endDate=$endDate_check;
        }

     
        $punchRecords = PunchRecord::with('punchInLocation','punchOutLocation')->whereBetween('punch_in_time', [$startDate, $endDate])
          ->orderBy('punch_in_time', 'desc') 
            ->get()
            ->groupBy(function($record) {
                return Carbon::parse($record->punch_in_time)->format('Y-m-d');
            });

        $data = ['punchRecords' => $punchRecords];

        return $this->returnJsonResponse(true, 'Punch records retrieved successfully', $data);
    } catch (\Exception $exception) {
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
}



    private function getLocationName($latitude, $longitude)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}");

        if ($response->successful() && $response['status'] === 'OK') {
            return $response['results'][0]['formatted_address'];
        }

        return 'Unknown Location';
    }

}
