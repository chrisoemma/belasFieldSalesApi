<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

  protected $fillable = [
    'name',
    'email',
    'phone_number',
    'latitude',
    'longitude',
  ];

//   protected $attributes = [
//     'status' => 'Active', 
// ];

  public function company_client()
  {
      return $this->hasMany(CompanyClient::class);
  }

  public function contactPersons()
    {
        return $this->hasMany(ClientContactPerson::class, 'client_id');
    }

    

}
