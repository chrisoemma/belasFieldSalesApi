<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'latitude',
        'longitude',
        'phone_number',
        'number_of_employees',
        'status',
        'company_img_url',
        'status',
        'doc_type',
        'doc_url',
        'doc_format'

    ];

    public function employees()

    {
        return $this->hasManyThrough(
            CompanyContactPerson::class,
            User::class,
            'company_id', // Foreign key in the User table
            'user_id'     // Foreign key in the CompanyContactPerson table
        );
    }

    public function company_clients()
{
    return $this->hasMany(CompanyClient::class, 'company_id', 'id');
}

public function clients()

{
    return $this->hasManyThrough(
        Client::class,           
        CompanyClient::class,    
        'company_id',          
        'id',                  
        'id',      
        'client_id'
    )->where('company_clients.status', 'Active');
}

public function contactPerson()

{
    return $this->hasManyThrough(
        ClientContactPerson::class, // Target model (ClientContactPerson)
        CompanyClient::class,      // Intermediate model (CompanyClient)
        'company_id',              // Foreign key in the intermediate model (CompanyClient)
        'client_id',               // Foreign key in the target model (ClientContactPerson)
        'id',                      // Local key in the source model (Company)
        'client_id'                // Local key in the intermediate model (CompanyClient)
    );
}
   
}
