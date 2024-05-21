<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable

{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'company_id',
        'profile_img',
        'can_mobile_login',
        'last_mobile_login',
        'otp',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
       return $this->belongsTo(Company::class);
    }

    public function contact_info(){
        return $this->hasOne(CompanyContactPerson::class,'user_id');
    }

    public function field_managers()
    
    {
        return $this->hasManyThrough(User::class, FieldManagerSalesPerson::class, 'sales_person_id', 'id', 'id', 'field_manager_id');
    }

    public function sales_people()
{
    return $this->hasMany(FieldManagerSalesPerson::class, 'sales_person_id');
}
}
