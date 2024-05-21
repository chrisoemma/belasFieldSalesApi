<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckIn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'purpose',
        'client_id',
        'user_id',
        'latitude',
        'longitude',
        'checkin_time',
        'status',
        'description',
        'title',
        'img_url',
        'task_id',
        'near_latitude',
        'near_longitude',
        'input_location',
        'checkin_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function task()
    {
        return $this->belongsTo(CheckIn::class, 'task_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assets()

    {
        return $this->hasMany(CheckInAsset::class);
    }

    public function check_outs()

    {
        return $this->hasMany(CheckOut::class);
    }
}
