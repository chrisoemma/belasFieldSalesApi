<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PunchRecord extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function punchInLocation()
    {
        return $this->belongsTo(Location::class,'punch_in_location_id');
    }

    public function punchOutLocation()

    {
        return $this->belongsTo(Location::class,'punch_out_location_id');
    }
}
