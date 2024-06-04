<?php

namespace App\Http\Controllers;

use App\Models\LeadStatusDefault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadStatusDefaultController extends Controller
{
    public function index()
    {
        try {
            $leadStatuses = LeadStatusDefault::all();
            $data = ['lead_status_defaults' => $leadStatuses];
            return $this->returnJsonResponse(true, 'Lead Status Defaults successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'flag' => 'nullable|string',
            'level' => 'nullable|integer'
        ]);

        try {
            $leadStatus = LeadStatusDefault::create($request->all());
            $data = ['lead_status_default' => $leadStatus];
            return $this->returnJsonResponse(true, 'Lead Status Default successfully created', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $leadStatus = LeadStatusDefault::findOrFail($id);
            $data = ['lead_status_default' => $leadStatus];
            return $this->returnJsonResponse(true, 'Lead Status Default successfully retrieved', $data);
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
            'level' => 'nullable|integer'
        ]);

        try {
            $leadStatus = LeadStatusDefault::findOrFail($id);
            $leadStatus->update($request->all());
            $data = ['lead_status_default' => $leadStatus];
            return $this->returnJsonResponse(true, 'Lead Status Default successfully updated', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy($id)
    {
        try {
            $leadStatus = LeadStatusDefault::findOrFail($id);
            $leadStatus->delete();
            return $this->returnJsonResponse(true, 'Lead Status Default successfully deleted', []);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


}
