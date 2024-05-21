<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyClient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
            'company_id',
            'client_id',
            'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function company()
    
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
