<?php

namespace App\Http\Controllers;

use App\Models\OpportunityStageConfigurable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpportunityStageConfigurableController extends Controller
{
    public function index()
    {
        try {
            $configurableStages = OpportunityStageConfigurable::all();
            $data = ['configurable_stages' => $configurableStages];
            return $this->returnJsonResponse(true, 'Configurable Opportunity Stages successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'flag' => 'required|string|in:continue,lost,won',
            'probability' => 'nullable|numeric|min:0|max:100',
            'company_id' => 'required|integer',
        ]);

        try {
            $configurableStage = OpportunityStageConfigurable::create($request->all());
            $data = ['configurable_stage' => $configurableStage];
            return $this->returnJsonResponse(true, 'Configurable Opportunity Stage successfully created', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $configurableStage = OpportunityStageConfigurable::findOrFail($id);
            $data = ['configurable_stage' => $configurableStage];
            return $this->returnJsonResponse(true, 'Configurable Opportunity Stage successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'flag' => 'required|string|in:continue,lost,won',
            'probability' => 'nullable|numeric|min:0|max:100',
            'company_id' => 'required|integer',
        ]);

        try {
            $configurableStage = OpportunityStageConfigurable::findOrFail($id);
            $configurableStage->update($request->all());
            $data = ['configurable_stage' => $configurableStage];
            return $this->returnJsonResponse(true, 'Configurable Opportunity Stage successfully updated', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy($id)
    {
        try {
            $configurableStage = OpportunityStageConfigurable::findOrFail($id);
            $configurableStage->delete();
            $data = ['configurable_stage' => $configurableStage];
            return $this->returnJsonResponse(true, 'Configurable Opportunity Stage successfully deleted', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

}
