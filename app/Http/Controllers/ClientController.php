<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContactPerson;
use App\Models\Company;
use App\Models\CompanyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    //

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "name" => "required",
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            $client = Client::create($request->all());

                ClientContactPerson::create([
                    'fist_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'country_id' => $request->country_id,
                    'client_id' => $client->id,
                    'position_id' => $request->position_id,
                ]);

                return $this->returnJsonResponse(true, 'Success', []);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function store_company_client($company_id, Request $request)
    {

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

                    $contact_person=ClientContactPerson::create([
                        'fist_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'phone_number' => $request->phone_number,
                        'country_id' => $request->country_id,
                        'client_id' => $client->id,
                        'position_id' => $request->position_id,
                    ]);

                }

                $data=[
                    'client'=>$client
                ];

                return $this->returnJsonResponse(true, 'Success', $data);
          

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

    public function update_company_client($client_id, Request $request)
    {
        try {
          //  $company = Company::findOrFail($company_id);

          //  $companyClient = CompanyClient::where('client_id', $client_id)
             //   ->firstOrFail();

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
            

            if ($request->has('first_name') || $request->has('last_name')) {
                $clientContactPerson = ClientContactPerson::where('client_id', $client_id)->first();
                if ($clientContactPerson) {
                    $clientContactPerson->update([
                        'fist_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'phone_number' => $request->phone_number,
                        'country_id' => $request->country_id,
                        'position_id' => $request->position_id,
                    ]);
                }
            }

            $data=[
                'client'=>$client
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

    public function destroy($id)
    {
        try {

            $client = Client::findOrFail($id);
            $companyClient = $client->company_client->first();


            if ($companyClient) {
                $companyClient->update(['status' => 'Banned']);
            }

            $data=[
              'client'=>$client
            ];

            return $this->returnJsonResponse(true, 'Client banned successfully',$data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function add_contact_person(Request $request, $client_id)
    {
        try {

            $client = Client::findorfail($client_id);

            $validator = Validator::make($request->all(), [
                'first_name' => 'required_without:last_name',
                'last_name' => 'required_without:first_name',
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            if ($request->has('first_name' || $request->has('last_name'))) {

                ClientContactPerson::create([
                    'fist_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'country_id' => $request->country_id,
                    'client_id' => $client->id,
                    'position_id' => $request->position_id,
                ]);

            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

    public function update_contact_person(Request $request, $client_id, $contact_person_id)
    {
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

            if ($request->has('first_name') || $request->has('last_name')) {
                $contactPerson->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'phone_number' => $request->input('phone_number'),
                    'country_id' => $request->input('country_id'),
                    'position_id' => $request->input('position_id'),
                ]);
            }

            return $this->returnJsonResponse(true, 'Contact person updated successfully', []);

        } catch (\Exception $exception) {
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
      try{

        $client = Client::find($client_id);  
        $contactPersons = $client->contactPersons->toArray();
        return $this->returnJsonResponse(true, 'Success', $contactPersons);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

}
