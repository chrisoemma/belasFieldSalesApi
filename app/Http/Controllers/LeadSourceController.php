<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadSourceController extends Controller
{
    public function index()
    {
        try {
            $leadSources = LeadSource::all();
            $data = ['lead_sources' => $leadSources];
            return $this->returnJsonResponse(true, 'Lead Sources successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'created_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();
        try {
            $leadSource = LeadSource::create([
                'name' => $request->input('name'),
                'created_by' => $request->input('created_by'),
                'updated_by' => $request->input('updated_by'),
            ]);
            DB::commit();
            $data = ['lead_source' => $leadSource];
            return $this->returnJsonResponse(true, 'Lead Source successfully created', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $leadSource = LeadSource::findOrFail($id);
            $data = ['lead_source' => $leadSource];
            return $this->returnJsonResponse(true, 'Lead Source successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'updated_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();
        try {
            $leadSource = LeadSource::findOrFail($id);
            $leadSource->update([
                'name' => $request->input('name'),
                'updated_by' => $request->input('updated_by'),
            ]);
            DB::commit();
            $data = ['lead_source' => $leadSource];
            return $this->returnJsonResponse(true, 'Lead Source successfully updated', $data);
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
            $leadSource = LeadSource::findOrFail($id);
            $leadSource->delete();
            DB::commit();
            $data = ['lead_source' => $leadSource];
            return $this->returnJsonResponse(true, 'Lead Source successfully deleted', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

   
}
