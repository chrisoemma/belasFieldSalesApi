<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'alias',
        'client_id'
    ];

    public function contactPersons()
    {
        return $this->belongsToMany(ClientContactPerson::class, 'contact_person_positions', 'position_id', 'contact_person_id');
    }
}
