<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $attributes = [
        'status' => 'Pending', 
        'priority'=>'Medium'
    ];




    protected $fillable = [
        'title',
        'decription',
        'due_date',
        'status',
        'assigned_to',
        'assigned_by',
        'priority',
        'created_by',
        'company_id',
        'lead_id',
    ];

     public function company()
     {
        return $this->belongsTo(Company::class,'company_id');
     }

     public function assigned_to()
     {
       return $this->belongsTo(User::class, 'assigned_to');
     }

     public function assigned_by()
     {
        return $this->belongsTo(User::class, 'assigned_by');
     }

     public function created_by()
     {
        return $this->belongsTo(User::class,'created_by');
     }

}
