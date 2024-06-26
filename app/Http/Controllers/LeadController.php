<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientContactPerson;
use App\Models\CompanyClient;
use App\Models\Lead;
use App\Models\LeadScoring;
use App\Models\LeadStatus;
use App\Models\Opportunity;
use App\Models\OpportunityStage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{

    public function company_leads($company_id)
    {
        try {

            $leads = Lead::with('client', 'leadStatus', 'manualScoring','owner','industry','source')
            ->where('company_id',$company_id)
            ->where('is_converted', false)->get();
            $data = ['leads' => $leads];
            return $this->returnJsonResponse(true, 'Lead Successfully retrived', $data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function person_leads($company_id,$user_id)
    {
        try {

            $leads = Lead::with('client', 'leadStatus', 'manualScoring','owner','industry','source')
            ->where('created_by',$user_id)
            ->where('company_id',$company_id)
            ->where('is_converted', false)->get();
            $data = ['leads' => $leads];
            return $this->returnJsonResponse(true, 'Lead Successfully retrived', $data);

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
        DB::beginTransaction();
        try {
            //create lead

            $validator = Validator::make($request->all(), [
                "last_name" => "required",
                "company_name" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 200);
            }
          
            $lead = Lead::create([
                'company_id' => $request->company_id,
                'client_name' => $request->company_name,
                'first_name' => $request->first_name,
                'website'=>$request->website,
                'last_name' => $request->last_name,
                'phone_number'=>$request->phone_number,
                'email'=>$request->email,
                'title'=>$request->title,
                'industry_id'=>$request->industry,
                'created_date' => Carbon::now(),
                'number_of_employees'=>$request->number_employees,
                'description'=>$request->description,
                'country' => $request->country,
                'created_by' => $request->creator,
                'source_id' => $request->lead_source,
            ]);

            $lead_scoring = LeadScoring::create([
                'manual_scoring_id' => $request->manual_scoring ? $request->manual_scoring : 1,
                'lead_id' => $lead->id,
                'created_by' => $request->creator,
            ]);

            LeadStatus::create([
                'lead_id' => $lead->id,
                'company_id' => $request->company_id,
                'status' => $request->lead_status,
                'created_date' => Carbon::now(),
                'created_by' => $request->creator,
                'lead_scoring_id' => $lead_scoring->id,
                'number_of_employees'=>$request->number_employees,
            ]);

            DB::commit();

            $data = [
                'lead' => Lead::with('client', 'leadStatus', 'manualScoring','owner','industry','source' )->find($lead->id),
            ];

            return $this->returnJsonResponse(true, 'Lead Successfully created', $data);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show($id)
    {
        try {
            $lead = Lead::with('client', 'leadStatus', 'manualScoring','owner')->findOrFail($id);
            $data = ['lead' => $lead];
            return $this->returnJsonResponse(true, 'Lead Successfully retrieved', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }
    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $lead = Lead::findOrFail($id);
             
            $lead->update([
                'company_id' => $request->company_id,
                'client_name' => $request->company_name,
                'first_name' => $request->first_name,
                'website'=>$request->website,
                'last_name' => $request->last_name,
                'phone_number'=>$request->phone_number,
                'email'=>$request->email,
                'title'=>$request->title,
                'industry_id'=>$request->industry,
                'updated_by' => $request->updated_by,
                'number_of_employees'=>$request->number_employees,
                'description'=>$request->description,
                'country' => $request->country,
                'source_id' => $request->lead_source,
                
            ]);

            // if ($request->has('lead_status')) {
            //     $lead->leadStatus()->update([
            //         'status' => $request->lead_status,
            //     ]);
            // }

            // if ($request->has('manual_scoring')) {
            //     $lead->manualScoring()->update([
            //         'manual_scoring_id' => $request->manual_scoring,
            //     ]);
            // }

            DB::commit();

            $data = ['lead' => $lead->load('client', 'leadStatus', 'manualScoring','owner','industry','source')];
            return $this->returnJsonResponse(true, 'Lead Successfully updated', $data);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy(Request $request,$id)
    {

        DB::beginTransaction();
        try {
            $lead = Lead::findOrFail($id);
            $lead->deleted_by=$request->deleted_by;
            $lead->save();
            $lead->delete();
            DB::commit();

            $data = [
                'lead' => $lead,
            ];
            return $this->returnJsonResponse(true, 'Lead Successfully deleted', $data);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }



    public function change_lead_status(Request $request, $lead_id)
{
    try {
        
        DB::beginTransaction();
        $lead = Lead::find($lead_id);

        $leadStatuses = Lead::getLeadStatuses($request->company_id);
        $requestLeadStatus = $leadStatuses->firstWhere('name', $request->lead_status);

        if ($requestLeadStatus) {
            LeadStatus::create([
                'lead_id' => $lead->id,
                'company_id' => $request->company_id,
                'status' => $request->lead_status,
                'created_date' => Carbon::now(),
                'created_by' => $request->creator,
            ]);

            if ($requestLeadStatus->flag == 'convert') {
                $lead->update([
                    'is_converted' => true,
                ]);

                $client = Client::where('name', $lead->client_name)
                    ->orWhere('email', $lead->email)
                    ->orWhere('phone_number', $lead->phone_number)
                    ->first();

                if (!$client) {
                    $client = Client::create([
                        'name' => $lead->client_name,
                        'email' => $lead->email,
                        'phone_number' => $lead->phone_number,
                    ]);
                }

                $client_id = $client->id;

                $companyClient = CompanyClient::where('company_id', $request->company_id)
                    ->where('client_id', $client_id)
                    ->first();

                if (!$companyClient && $request->client_name) {
                    CompanyClient::create([
                        'company_id' => $lead->company_id,
                        'client_id' => $client_id,
                        'status' => 'Active',
                    ]);
                }

                $contactPerson = ClientContactPerson::where('email', $lead->email)
                    ->orWhere('phone_number', $lead->phone_number)
                    ->first();

                if (!$contactPerson) {
                    $contactPerson = ClientContactPerson::create([
                        'client_id' => $client_id,
                        'name' => $lead->contact_name,
                        'email' => $lead->email,
                        'phone_number' => $lead->phone_number,
                    ]);
                }

                $closeDate = Carbon::now()->addMonth()->endOfMonth();
                $opportunity = Opportunity::create([
                    'name' => $lead->name,
                    'company_id' => $request->company_id,
                    'client_id' => $client_id,
                    'lead_id' => $lead->id,
                    'close_date' => $closeDate,
                    'created_date' => Carbon::now(),
                    'created_by' => $request->creator,
                    'description' => $lead->description,
                ]);

                $stages = Opportunity::getOpportunityStages($request->company_id);

                $stage = $stages->firstWhere('level', 1);
    
                OpportunityStage::create([
                    'opportunity_id' => $opportunity->id,
                    'stage' => $stage->name,
                    'created_date' => Carbon::now(),
                    'created_by' => $request->creator,
                    'probability' => $stage->probability,
                ]);
            }

            DB::commit();
        }

        $data = [
            'lead' => Lead::with('client', 'leadStatus', 'manualScoring','owner','industry','source')->find($lead->id),
        ];
        return $this->returnJsonResponse(true, 'Status successfully updated', $data);

    } catch (\Exception $exception) {
        DB::rollBack();
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
}

    public function  currentLeadStatus($company_id)

    {
        try{
            $leadStatuses = Lead::getLeadStatuses($company_id);

            $data=[
              'lead_statuses'=>$leadStatuses
            ];
            return $this->returnJsonResponse(true, 'Lead Statuses retrived', $data);
          } catch (\Exception $exception) {
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
    
    }

}
