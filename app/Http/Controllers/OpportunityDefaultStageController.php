<?php

namespace App\Http\Controllers;

use App\Models\OpportunityDefaultStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OpportunityDefaultStageController extends Controller
{
    public function index()
    {
        
        try {
            $defaultStages = OpportunityDefaultStage::all();

            $data = ['default_stages' => $defaultStages];
            return $this->returnJsonResponse(true, 'Default Opportunity Stages successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
        ]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();
        try {
            $defaultStage = OpportunityDefaultStage::create($request->all());
            DB::commit();
            $data = ['default_stage' => $defaultStage];
            return $this->returnJsonResponse(true, 'Default Opportunity Stage successfully created', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $defaultStage = OpportunityDefaultStage::findOrFail($id);
            $data = ['default_stage' => $defaultStage];
            return $this->returnJsonResponse(true, 'Default Opportunity Stage successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $defaultStage = OpportunityDefaultStage::findOrFail($id);
            $defaultStage->update($request->all());
            DB::commit();
            $data = ['default_stage' => $defaultStage];
            return $this->returnJsonResponse(true, 'Default Opportunity Stage successfully updated', $data);
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
            $defaultStage = OpportunityDefaultStage::findOrFail($id);
            $defaultStage->delete();
            DB::commit();
            $data=[
                'default_stage'=>$defaultStage
            ];
            return $this->returnJsonResponse(true, 'Default Opportunity Stage successfully deleted', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

}
