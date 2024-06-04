<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Opportunity extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded =[];


    public static function getOpportunityStages($companyId) {

        $config = DB::table('configurables')
                    ->where('company_id', $companyId)
                    ->where('table_name', 'opportunity_stage_configurables')
                    ->first();
    
        if ($config && $config->is_configurable) {
            return DB::table('opportunity_stage_configurables')
                     ->where('company_id', $companyId)
                     ->get();
        } else {
            return DB::table('opportunity_default_stages')->get();
        }
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contactPeople()
    {
        return $this->hasManyThrough(
            ClientContactPerson::class, 
            OpportunityContactPerson::class,  
            'opportunity_id',     
            'id',     
            'id',                     
            'client_contact_people_id'  
        );
    }

    public function OpportunityStages()
    {
        return $this->hasMany(OpportunityStage::class);
    }

    public function forecast()
    {
        return $this->belongsTo(OpportunityForecast::class, 'opportunity_forecast_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
