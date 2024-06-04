<?php

namespace App\Http\Controllers;

use App\Models\OpportunityForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OpportunityForecastController extends Controller
{
    public function index()
    {
        try {
            $forecasts = OpportunityForecast::all();
            $data = ['forecasts' => $forecasts];
            return $this->returnJsonResponse(true, 'Opportunity Forecasts successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'level' => 'nullable|integer',
            'flag' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();
        try {
            $forecast = OpportunityForecast::create($request->all());
            DB::commit();
            $data = ['forecast' => $forecast];
            return $this->returnJsonResponse(true, 'Opportunity Forecast successfully created', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $forecast = OpportunityForecast::findOrFail($id);
            $data = ['forecast' => $forecast];
            return $this->returnJsonResponse(true, 'Opportunity Forecast successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'level' => 'nullable|integer',
            'flag' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();
        try {
            $forecast = OpportunityForecast::findOrFail($id);
            $forecast->update($request->all());
            DB::commit();
            $data = ['forecast' => $forecast];
            return $this->returnJsonResponse(true, 'Opportunity Forecast successfully updated', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $forecast = OpportunityForecast::findOrFail($id);
            $forecast->delete();
            DB::commit();
            $data = ['forecast' => $forecast];
            return $this->returnJsonResponse(true, 'Opportunity Forecast successfully deleted', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


}
