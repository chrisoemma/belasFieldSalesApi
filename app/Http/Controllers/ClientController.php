<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContactPerson;
use App\Models\Company;
use App\Models\CompanyClient;
use App\Models\ContactPersonPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    //
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                "name" => "required",
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            $client = Client::create($request->all());

            if($request->company_id){
                 CompanyClient::create([
                'company_id' => $request->company_id,
                'client_id' => $client->id,
                'status' => 'Active',
            ]);
        }

            DB::commit();
            $data = [
                'client' => $client,
            ];
            return $this->returnJsonResponse(true, 'Success', $data);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function update($client_id, Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                "name" => "required",
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            // Update the associated client record
            $client = Client::find($client_id);

            $client->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);


            DB::commit();

            $data = [
                'client' => $client,
            ];
            return $this->returnJsonResponse(true, 'Success', $data);
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }



    public function destroy(Request $request, $client_id)
{
    DB::beginTransaction();
    try {
        $client = Client::findOrFail($client_id);
        $client->delete();

        CompanyClient::where('client_id',$client->id)->delete();

        DB::commit();

        $data=[
         'client'=>$client
        ];
        return $this->returnJsonResponse(true, 'Client deleted successfully', $data);
    } catch (\Exception $exception) {
        DB::rollBack();
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
}
    public function store_company_client($company_id, Request $request)
    {
        DB::beginTransaction();
        try {

            $company = Company::findorfail($company_id);

            $validator = Validator::make($request->all(), [
                "name" => "required",
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            $client = Client::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            CompanyClient::create([
                'company_id' => $company_id,
                'client_id' => $client->id,
                'status' => 'Active',
            ]);

            if ($request->has('first_name' || $request->has('last_name'))) {

                $contact_person = ClientContactPerson::create([
                    'fist_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'country_id' => $request->country_id,
                    'client_id' => $client->id,
                    'position_id' => $request->position_id,
                ]);

            }

            $data = [
                'client' => $client,
            ];

            return $this->returnJsonResponse(true, 'Success', $data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }



    public function show($id)
    {
        try {
            // Find the client by client_id
            $client = Client::findOrFail($id);

            return $this->returnJsonResponse(true, 'Client retrieved successfully', [
                'client' => $client,
                'company' => $client->company_client->first()->company,
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    // public function destroy($id)
    // {
    //     try {

    //         $client = Client::findOrFail($id);
    //         $companyClient = $client->company_client->first();

    //         if ($companyClient) {
    //             $companyClient->update(['status' => 'Banned']);
    //         }

    //         $data = [
    //             'client' => $client,
    //         ];

    //         return $this->returnJsonResponse(true, 'Client banned successfully', $data);

    //     } catch (\Exception $exception) {
    //         Log::error($exception->getMessage());
    //         return $this->returnJsonResponse(false, $exception->getMessage(), []);
    //     }
    // }

    public function add_contact_person(Request $request, $client_id)
    {

        DB::beginTransaction();
        try {

            $client = Client::findorfail($client_id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required_without:last_name',
                'last_name' => 'required_without:first_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 401);
            }

            $contact_person = ClientContactPerson::create([
                'fist_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_id' => $request->country_id,
                'client_id' => $client->id,
                'position_id' => $request->position_id,
            ]);

            if(!empty($request->positions)){
                foreach($request->positions as $position)
                 ContactPersonPosition::create([
                   'position_id'=>$position,
                   'contact_person_id'=>$contact_person->id
                 ]);
            }

            DB::commit();


            $contactPerson = ClientContactPerson::with('positions')->where('id', $contact_person->id)->first();
        

            $data = [
                'contact_person' => $contactPerson,
            ];
            return $this->returnJsonResponse(true, 'Contact person created successfully', $data);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

    public function update_contact_person(Request $request, $client_id, $contact_person_id)
    {
        DB::beginTransaction();
        try {
            $client = Client::findOrFail($client_id);
    
            $contactPerson = ClientContactPerson::findOrFail($contact_person_id);
    
            $validator = Validator::make($request->all(), [
                'first_name' => 'required_without:last_name',
                'last_name' => 'required_without:first_name',
            ]);
    
            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }
    
            $contactPerson->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'country_id' => $request->country_id,
                'position_id' => $request->position_id,
            ]);
    
       
            if (!empty($request->positions)) {
                ContactPersonPosition::where('contact_person_id', $contact_person_id)->delete();
                foreach ($request->positions as $position) {
                    ContactPersonPosition::create([
                        'position_id' => $position,
                        'contact_person_id' => $contact_person_id
                    ]);
                }
            }
            DB::commit();
    
            $contact_person = ClientContactPerson::with('positions')->where('id', $contactPerson->id)->first();
    
            $data = [
                'contact_person' => $contact_person,
            ];
            return $this->returnJsonResponse(true, 'Contact person updated successfully', $data);
    
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }
    

    // public function clients($company_id)

    // {

    //   try{
    //         $clients=Company::with('company_clients')->where('id',$company_id)->get();

    //         return $this->returnJsonResponse(true, 'Clients retrieved successfully', [
    //             'clients' => $clients,
    //         ]);

    //   } catch (\Exception $exception) {
    //         Log::error($exception->getMessage());
    //         return $this->returnJsonResponse(false, $exception->getMessage(), []);
    //     }
    // }

    public function contact_people($client_id)
    {

        try {

            $client = Client::find($client_id);
            $contactPeople = ClientContactPerson::with('positions')->where('client_id', $client->id)
                ->get();

                $data=[
                  'contact_people'=>$contactPeople
                ];
            return $this->returnJsonResponse(true, 'Success', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function delete_contact_person(Request $request, $client_id, $contact_person_id)
    {
        DB::beginTransaction();
        try {

            $client = Client::findOrFail($client_id);

            $contact_person = ClientContactPerson::where('id', $contact_person_id)
                ->firstOrFail();
            $contact_person->delete();
            DB::commit();
            $data = [
                'contact_person' => $contact_person,
            ];

            return $this->returnJsonResponse(true, 'Contact person deleted successfully', $data);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

}
