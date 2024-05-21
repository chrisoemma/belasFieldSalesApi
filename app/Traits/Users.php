<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

trait Users
{
    public function getOrCreateUser($company,$userDetails, $notify = false) {

        // return $userDetails;
      
        try {
            $otp = mt_rand(100000, 999999);
            $userDetails['otp'] = Hash::make($otp);
            $userDetails['password'] = Hash::make($userDetails['password']);
            $userDetails['email'] = $userDetails['email'];
            $userDetails['phone_number']=$userDetails['phone_number'];
            $userDetails['company_id']=$company->id;
           
            $user = User::firstOrCreate(
                ["name" => $userDetails['first_name'].' '.$userDetails['last_name']],
                $userDetails
            );

         //   return $user;

          //  $notify ? $user->notify(new UserCreated($user, $otp)) : null;

            return $user;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            return null;
        }
    }
}
