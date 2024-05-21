<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyContactPerson;
use App\Models\FieldManagerSalesPerson;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Usercontroller extends Controller
{
    public function index()
    {
        try {

            $users = User::with('company', 'contact_info')->get();

            $data = [
                'users' => $users,
            ];

            return $this->returnJsonResponse(true, 'Success', $data);

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

    public function store(Request $request)

    {
        try {

            $validator = Validator::make($request->all(), [
                "name" => "required",
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            if ($user = User::create($request->all())) {

                CompanyContactPerson::create([
                    'fist_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'email'=>$request->email,
                    'phone_number'=>$request->phone_number,
                    'country_id'=>$request->country_id,
                    'company_id'=>$request->company_id,
                    'position_id'=>$request->position_id,
                    'user_id'=>$user->id,
                    'status'=>'In Active'
                ]);

                return $this->returnJsonResponse(true, 'Success', []);
            } else {
                return $this->returnJsonResponse(false, "Something went wrong", []);
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function sales_people($company_id, $user_id)
    {
        try {
            $sales_people = FieldManagerSalesPerson::with(['user'])
                ->where('field_manager_id', $user_id)
                ->where('company_id', $company_id)
                ->get();
    
            return $this->returnJsonResponse(true, 'Salespeople retrieved successfully', $sales_people);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }
  
    
    
}

    

