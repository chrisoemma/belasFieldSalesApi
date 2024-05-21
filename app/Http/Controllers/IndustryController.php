<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IndustryController extends Controller
{
  
    public function index()
    {
        try {

            $industries = Industry::all();

            return $this->returnJsonResponse(true, 'Industries retrieved successfully', [
                'industries' => $industries,

            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

 
    public function create()
    {
        
    }


    public function store(Request $request)

    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }
            if ($industry =Industry::create([
                'name' => $request->name,
            ])) {

                return $this->returnJsonResponse(true, 'Success', []);
            } else {
                return $this->returnJsonResponse(false, "Something went wrong", []);
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
     
            $industry = Industry::find($id);
    
            if (!$industry) {
                return $this->returnJsonResponse(false, 'Industry not found', []);
            }
    
            return $this->returnJsonResponse(true, 'Success', ['industry' => $industry]);
    
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $industry = Industry::find($id);
    
            if (!$industry) {
                return $this->returnJsonResponse(false, 'Industry not found', []);
            }
    
            $validator = Validator::make($request->all(), [
                'name' => 'required',
         
            ]);
    
            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }
    
            $industry->update([
                'name' => $request->name,
            ]);
    
            return $this->returnJsonResponse(true, 'Industry updated successfully', ['position' => $position]);
    
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function destroy($id)
    {
        try {
            $industry = Industry::find($id);
    
            if (!$industry) {
                return $this->returnJsonResponse(false, 'Industry not found', []);
            }
    
            $industry->delete();
    
            return $this->returnJsonResponse(true, 'Industry deleted successfully', []);
    
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }
}
