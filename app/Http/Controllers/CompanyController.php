<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{

    public function index()
    
    {
        try {

            $companies = Company::all();

            $data = [
                'companies' => $companies,
            ];

            return $this->returnJsonResponse(true, 'Success', $data);

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function show(Company $company)
    {
        try {

            $data = [
                'company' => $company,
            ];

            return $this->returnJsonResponse(true, 'Success', $data);

        } catch (\Exception $exception) {

            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function employees($company_id)
    {

        try {

            $company = Company::with('employees')->find($company_id);

            if (!$company) {
                return $this->returnJsonResponse(false, 'Employees not found', []);
            }
            $data = [
                'company' => $company,
            ];

            return $this->returnJsonResponse(true, 'Company Employees retrieved successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function company_clients($company_id)
    {

        try {
            $company = Company::find($company_id);

            if (!$company) {
                return $this->returnJsonResponse(false, 'Clients not found', []);
            }

            $company = Company::find($company_id);

            if (!$company) {
                return $this->returnJsonResponse(false, 'Clients not found', []);
            }
            
            $clients = $company->clients;
            
            $cleanedClients = [];
            foreach ($clients as $client) {
                
                $clientArray = $client->toArray();
                unset($clientArray['laravel_through_key']);
                $cleanedClients[] = $clientArray;
            }
            
            $data = [
                'clients' => $cleanedClients,
            ];
            return $this->returnJsonResponse(true, 'Company Clients retrieved successfully', $data);
      
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

}
