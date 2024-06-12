<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanyClient;
use App\Models\Opportunity;
use App\Models\OpportunityContactPerson;
use App\Models\OpportunityStage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OpportunityController extends Controller
{
    //
    public function index()
    {
        try {
            $oppotunities = Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client','owner')->get();
            $data = ['opportunities' => $oppotunities];
            return $this->returnJsonResponse(true, 'Opportunities Successfully retrived', $data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function company_opportunities($company_id)
    {
        try {

            $oppotunities = Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client','owner')
            ->where('company_id',$company_id)
            ->get();
            $data = ['opportunities' => $oppotunities];
            return $this->returnJsonResponse(true, 'Opportunities Successfully retrived', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function sales_rep_opportunities($company_id,$user_id)
    {
        try {

            $oppotunities = Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client','owner')
            ->where('created_by',$user_id)
            ->where('company_id',$company_id)
            ->get();
            $data = ['opportunities' => $oppotunities];
            return $this->returnJsonResponse(true, 'Opportunities Successfully retrived', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }



    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                "opportunity_name" => "required",
                "close_date" => "required",
                "forecast" => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 200);
            }

            if ($request->client_name) {
                $client = Client::create([
                    'name' => $request->client_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                ]);
                $client_id = $client->id;
            } else {
                $client_id = $request->client;
            }
            //  create company_client
            if ($request->client_name) {
                CompanyClient::create([
                    'company_id' => $request->company_id,
                    'client_id' => $client->id,
                    'status' => 'Active',
                ]);
            }
            $opportunity = Opportunity::create([
                'name' => $request->opportunity_name,
                'company_id' => $request->company_id,
                'client_id' => $client_id,
                'close_date' => $request->close_date,
                'created_date' => Carbon::now(),
                'created_by' => $request->creator,
                'source_id' => $request->source,
                'description' => $request->decription,
                'amount' => $request->amount,
                'opportunity_forecast_id' => $request->forecast,
            ]);

            $stages = Opportunity::getOpportunityStages($request->company_id);

            $stage = $stages->firstWhere('name', $request->stage);

            OpportunityStage::create([
                'opportunity_id' => $opportunity->id,
                'stage' => $request->stage,
                'created_date' => Carbon::now(),
                'created_by' => $request->creator,
                'amount' => $request->amount,
                'probability' => $request->probability ? $request->probability : $stage->probability,
            ]);

            DB::commit();

            $data = [
                'opportunity' => Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client','owner')->find($opportunity->id),
            ];

            return $this->returnJsonResponse(true, 'Opportunity Successfully created', $data);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }



    public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $validator = Validator::make($request->all(), [
            "opportunity_name" => "required",
            "close_date" => "required",
            "forecast" => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 200);
        }

        $opportunity = Opportunity::findOrFail($id);

        if ($request->client_name) {
            $client = Client::create([
                'name' => $request->client_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);
            $client_id = $client->id;
        } else {
            $client_id = $request->client;
        }

        if ($request->client_name) {
            CompanyClient::create([
                'company_id' => $request->company_id,
                'client_id' => $client->id,
                'status' => 'Active',
            ]);
        }

        $opportunity->update([
            'name' => $request->opportunity_name,
            'company_id' => $request->company_id,
            'client_id' => $client_id,
            'close_date' => $request->close_date,
            'description' => $request->decription,
            'amount' => $request->amount,
            'updated_by'=>$request->updated_by,
            'opportunity_forecast_id' => $request->forecast,
        ]);

        $stages = Opportunity::getOpportunityStages($request->company_id);

        $stage = $stages->firstWhere('name', $request->stage);

        $opportunityStage = OpportunityStage::where('opportunity_id', $opportunity->id)->first();

        if ($opportunityStage) {
            $opportunityStage->update([
                'stage' => $request->stage,
                'amount' => $request->amount,
                'probability' => $request->probability ? $request->probability : $stage->probability,
            ]);
        }
        DB::commit();

        $data = [
            'opportunity' => Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client', 'owner')->find($opportunity->id),
        ];

        return $this->returnJsonResponse(true, 'Opportunity Successfully updated', $data);

    } catch (\Exception $exception) {
        DB::rollBack();
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
}



    public function destroy(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $opportunity = Opportunity::find($id);

        if (!$opportunity) {
            return response()->json([
                'status' => false,
                'error' => 'Opportunity not found',
            ], 404);
        }

        $opportunity->deleted_by = $request->deleted_by; 
        $opportunity->save();

        $opportunity->delete();

        DB::commit();

        $data = [
            'opportunity' =>$opportunity,
        ];

        return $this->returnJsonResponse(true, 'Opportunity successfully deleted', $data);
        
    } catch (\Exception $exception) {
        DB::rollBack();
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
}


    public function add_contact_person(Request $request, $opportunity_id)

    {
        try {
            $opportunity = Opportunity::findorfail($opportunity_id);
            DB::beginTransaction();
            OpportunityContactPerson::create([
                'client_contact_person_id' => $request->contact_prson_id,
                'opportunity_id' => $opportunity_id,
            ]);

            DB::commit();
            $data = [
                'opportunity' => Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client','owner')->find($opportunity->id),
            ];
            return $this->returnJsonResponse(true, 'Contact person added Successfully', $data);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show()
    {

    }

    public function change_opportunity_stage(Request $request, $opportunity_id)
    {
        DB::beginTransaction();
        try {
            $opportunity = Opportunity::findorfail($opportunity_id);
            $opportunityStages = Opportunity::getOpportunityStages($request->company_id);
            $requestOpportunityStage = $opportunityStages->firstWhere('name', $request->stage);
        
            OpportunityStage::create([
                'opportunity_id' => $opportunity->id,
                'stage' => $request->stage,
                'created_date' => Carbon::now(),
                'created_by' => $request->creator,
                'amount' => $request->amount,
                'status'=>$request->status,
                'probability' => $request->probability ? $request->probability : $requestOpportunityStage->probability,
            ]);

            //
            DB::commit();
            $data = [
                'opportunity' => Opportunity::with('OpportunityStages', 'forecast', 'contactPeople', 'client','owner')->find($opportunity->id),
            ];

            return $this->returnJsonResponse(true, 'Stage updated Successfully', $data);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }


    public function  currentOppoturnityStages($company_id)

    {
        try{
            $opportunityStages = Opportunity::getOpportunityStages($company_id);

            $data=[
              'opportunity_stages'=>$opportunityStages
            ];
            return $this->returnJsonResponse(true, 'Opportunity stages retrived', $data);

          } catch (\Exception $exception) {
        Log::error($exception->getMessage());
        return $this->returnJsonResponse(false, $exception->getMessage(), []);
    }
    }
}
