<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyContactPerson extends Model
{
    use HasFactory;
    use SoftDeletes;

   protected $fillable=[
    'fist_name',
    'last_name',
    'email',
    'secondary_email',
    'phone_number',
    'alt_phone_number',
    'country_id',
    'company_id',
    'gender',
    'prefered_channel',
    'position_id',
    'user_id',
   ];
}
