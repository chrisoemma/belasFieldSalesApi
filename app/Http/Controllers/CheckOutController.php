<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\RescheduledMeeting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckOutController extends Controller
{

     public function check_out(Request $request, $check_in_id)

     {
          try{
        $check_in=CheckIn::find($check_in_id);

          $check_in->update([
            'status'=>$request->status
           ]);

        if($request->status=='Rescheduled'){
            //create a rescheduled table
            RescheduledMeeting::create([
                'check_in_id'=>$check_in->id,
                'original_meeting_datetime'=>$check_in->checkin_time,
                'rescheduled_meeting_datetime'=>$request->rescheduled_time,
                'rescheduled_meeting_location'=>$request->meeting_location,
            ]);
        }
        //create a checkout, //rescheduled
        $checkout=CheckOut::create([
            'check_in_id'=>$check_in->id,
            'latitude'=>$request->latitude, 
            'longitude'=>$request->longitude, 
            'status'=>$request->status,
            'input_location'=>$request->input_location,
            'near_latitude'=>$request->near_latitude, 
            'near_longitude'=>$request->near_latitude,
            'comments'=>$request->comment,
            'checkout_time'=>Carbon::now(),
            'user_id'=>$request->user_id
        ]);

        $checkIn=CheckIn::with('user','task','client','assets','check_outs','check_outs.assets')->find($check_in->id);

        $data=[
         'checkIn' =>$checkIn
        ];
            
        return $this->returnJsonResponse(true, 'Checkout  successfully', $data);
            
} catch (\Exception $exception) {
    Log::error($exception->getMessage());
    return $this->returnJsonResponse(false, $exception->getMessage(), []);
}
    
    }


}
