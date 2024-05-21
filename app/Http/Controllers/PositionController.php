<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
   

    public function index()

    {
        
        try {

            $positions = Position::all();

            return $this->returnJsonResponse(true, 'Positions retrieved successfully', [
                'positions' => $positions,

            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }


    public function create()
    {
        //
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
            if ($position = Position::create([
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
            // Assuming you have a "Position" model and want to retrieve it by its ID
            $position = Position::find($id);
    
            if (!$position) {
                return $this->returnJsonResponse(false, 'Position not found', []);
            }
    
            return $this->returnJsonResponse(true, 'Success', ['position' => $position]);
    
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $position = Position::find($id);
    
            if (!$position) {
                return $this->returnJsonResponse(false, 'Position not found', []);
            }
    
            $validator = Validator::make($request->all(), [
                'name' => 'required',
         
            ]);
    
            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }
    
            $position->update([
                'name' => $request->name,
            ]);
    
            return $this->returnJsonResponse(true, 'Position updated successfully', ['position' => $position]);
    
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function destroy($id)
    {
        try {
            $position = Position::find($id);
    
            if (!$position) {
                return $this->returnJsonResponse(false, 'Position not found', []);
            }
    
            $position->delete();
    
            return $this->returnJsonResponse(true, 'Position deleted successfully', []);
    
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }
}
