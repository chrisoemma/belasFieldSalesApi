<?php

namespace App\Http\Controllers;

use App\Models\LeadStatusConfigurable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadStatusConfigurableController extends Controller
{
    public function index()
    {
        try {
            $leadStatuses = LeadStatusConfigurable::all();
            $data = ['lead_status_configurables' => $leadStatuses];
            return $this->returnJsonResponse(true, 'Lead Status Configurables successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|integer',
            'flag' => 'nullable|string',
            'level' => 'nullable|integer'
        ]);

        try {
            $leadStatus = LeadStatusConfigurable::create($request->all());
            $data = ['lead_status_configurable' => $leadStatus];
            return $this->returnJsonResponse(true, 'Lead Status Configurable successfully created', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            
            $leadStatus = LeadStatusConfigurable::findOrFail($id);
            $data = ['lead_status_configurable' => $leadStatus];
            return $this->returnJsonResponse(true, 'Lead Status Configurable successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'flag' => 'nullable|string',
            'level' => 'nullable|integer',
            'company_id' => 'required|integer'
        ]);

        try {
            $leadStatus = LeadStatusConfigurable::findOrFail($id);
            $leadStatus->update($request->all());
            $data = ['lead_status_configurable' => $leadStatus];
            return $this->returnJsonResponse(true, 'Lead Status Configurable successfully updated', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy($id)
    {
        try {
            $leadStatus = LeadStatusConfigurable::findOrFail($id);
            $leadStatus->delete();
            $data=[
              'leadStatus'=>$leadStatus
            ];
            return $this->returnJsonResponse(true, 'Lead Status Configurable successfully deleted',$data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


}
