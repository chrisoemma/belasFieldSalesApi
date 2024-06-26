<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'client_name',
        'first_name',
        'phone_number',
        'last_name',
        'gender',
        'email',
        'created_date',
        'industry_id',
        'title',
        'country_id',
        'country',
        'position_id',
        'created_by',
        'source_id',
        'is_converted',
        'website',
        'number_of_employees',
        'description',
        'deleted_by',
        'updated_by'
        

    ];

    public static function getLeadStatuses($companyId)
    
    {
        $config = DB::table('configurables')
        ->where('company_id', $companyId)
        ->where('table_name', 'lead_status_configurable')
        ->first();

        if ($config && $config->is_configurable) {
            return DB::table('lead_status_configurable')
                     ->where('company_id', $companyId)
                     ->orderBy('level')
                     ->get();
        } else {
            return DB::table('lead_status_defaults')
            ->orderBy('level')
            ->get();
        }
    }

    public function leadStatus()
    {
        return $this->hasMany(LeadStatus::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function leadScorings()
    {
        return $this->hasMany(LeadScoring::class);
    }

    public function manualScoring()
    {
        return $this->hasOneThrough(
            ManualScoring::class,
            LeadScoring::class,
            'lead_id',
            'id',
            'id',  
            'manual_scoring_id' 
        );
    }

    public function contactPeople()
    {
        return $this->hasManyThrough(
            ClientContactPerson::class, 
            LeadContactPerson::class,  
            'lead_id',     
            'id',     
            'id',                     
            'client_contact_people_id'  
        );
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function source()
    {
        return $this->belongsTo(LeadSource::class,'source_id');
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
