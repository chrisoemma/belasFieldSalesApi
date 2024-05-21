<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\checkInAsset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckInController extends Controller
{

    public function store(Request $request, $user_id)
    {
        try {
            
            $user = User::find($user_id);

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'purpose' => 'required',
              //  'client' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            $check_in = CheckIn::create([
                'user_id' => $user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'checkin_time' => $request->time,
                'description' => $request->description,
                'title' => $request->title,
                'purpose' => $request->purpose,
                'client_id' => $request->client,
                'task_id' => $request->task,
                'checkin_time' => Carbon::now(),
                'input_location' => $request->input_location,
                'checkin_by' => $user_id,
            ]);

            if ($request->photos) {
                foreach ($request->photos as $photo) {
                    checkInAsset::create([
                        'check_in_id'=>$check_in->id,
                        'url' => $photo['url'],
                        'type' => $photo['type'],
                    ]);
                }
            }
            $checkIn = CheckIn::with('user', 'task', 'client', 'assets')->find($check_in->id);

            return $this->returnJsonResponse(true, 'Check Ins successfully', [
                'checkIn' => $checkIn,
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function checks_ins_outs_by_user($request_type, $user_id)
    {
        try {
            $user = User::find($user_id);

          

            $checks = CheckIn::with('user', 'task', 'client', 'assets', 'check_outs', 'check_outs.assets')->where('user_id', $user_id);

            if ($request_type === 'check_in') {
                $checks->whereIn('status', ['In Progress', 'Rescheduled']);
            } elseif ($request_type === 'check_out') {
                $checks->whereIn('status', ['Completed', 'Canceled']);
            }
            $result = $checks->get();
            $data = [
                'checks' => $result,
            ];
            return $this->returnJsonResponse(true, 'Success', $data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function check_ins_outs_by_company(Request $request, $company_id)
    {
        try {

            $users = User::where('company_id', $company_id)->get();

            if ($users->isEmpty()) {
                return $this->returnJsonResponse(false, 'No users found for the specified company', []);
            }

            $request_type = $request->requestType;

            $query = CheckIn::whereIn('user_id', $users->pluck('id'));

            if ($request_type === 'check_in') {
                $query->where('status', 'open');
            } elseif ($request_type === 'check_out') {
                $query->where('status', 'closed');
            } else {
                // Handle invalid request types here
            }
            $result = $query->get();

            $data = [
                'checks' => $result,
            ];

            return $this->returnJsonResponse(true, 'Data retrieved successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update_checkIns(Request $request, $check_in_id)
    {

        try {

            $check_in = CheckIn::find($check_in_id);

            $check_in->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'description' => $request->description,
                'title' => $request->title,
                'img_url' => $request->img_url,
            ]);

            $data = [
                'check_in' => $check_in,
            ];

            return $this->returnJsonResponse(true, 'Data updated Successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function check_out($user_id, $check_in_id)
    {
        try {
            $user = User::find($user_id);

            $checkIn = checkIn::find($check_in_id);
            $checkIn->update([
                'status' => 'closed',
            ]);

            $data = [
                'checkIn' => $checkIn,
            ];

            return $this->returnJsonResponse(true, 'Data updated Successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

}
