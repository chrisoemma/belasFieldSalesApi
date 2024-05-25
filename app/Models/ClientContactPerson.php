<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientContactPerson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'fist_name',
        'last_name',
        'email',
        'secondary_email',
        'phone_number',
        'alt_phone_number',
        'country_id',
        'client_id',
        'gender',
        'prefered_channel',
        'position_id',
    ];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'contact_person_positions', 'contact_person_id', 'position_id');
    }
}
