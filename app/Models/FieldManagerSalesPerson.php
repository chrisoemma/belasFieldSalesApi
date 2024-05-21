<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldManagerSalesPerson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable=[
        'field_manager_id',
        'sales_person_id',
        'company_id',
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'sales_person_id');
}

public function company()
{
    return $this->belongsTo(Company::class);
}
}
