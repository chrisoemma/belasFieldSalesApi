<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\CompanyContactPerson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordResetRequested;
use App\Notifications\UserCreated;
use Illuminate\Support\Facades\DB;
use App\Traits\Users;

class AuthController extends Controller
{
    use Users;

    public function loginByPhone(Request $request)

    {

      
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
            ], 401);
        } else {
            if (Auth::attempt(['phone_number' => request('phone_number'), 'password' => request('password')])) {
                $user = User::with('company')->find(Auth::id());
                $token = $user->createToken($user->id)->plainTextToken;
                return response()->json([
                    'status' => true,
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'error' => "Please check your credentials $request->phone_number",
                ], 401);
            }
        }
    }

    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                //'name' => 'required|min:3',
                'phone_number' => 'required|unique:users',
                'password' => 'required|min:4',
                'company_name'=>'required|min:3'
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 200);

            } else {
               $company=Company::create([
                    'name'=>$request->company_name,
                    'email'=>$request->company_email,
                    'latitude'=>$request->latitude,
                    'longitude'=>$request->longitude,
                    'phone_number'=>$request->company_phone_number,
                    'number_of_employees'=>$request->number_of_employees,
                    'status'=>$request->status,
                    'company_img_url'=>$request->company_img_url,
                    'doc_type'=>$request->doc_type,
                    'doc_url'=>$request->doc_url,
                    'doc_format'=>$request->doc_format,
                    'industry_id'=>$request->industry_id
                ]);

                $otp = mt_rand(100000, 999999);
             

                $userDetails = [
                    'name' => $request->first_name.' '.$request->last_name,
                    'otp' => Hash::make($otp),
                    'password' => Hash::make($request->password),
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'company_id' => $company->id,
                ];
                
               
                $user = User::firstOrCreate(
                   $userDetails
                );
    
                 $user->notify(new UserCreated($user, $otp));
              
                    CompanyContactPerson::create([
                        'fist_name'=>$request->first_name,
                        'last_name'=>$request->last_name,
                        'email'=>$request->email,
                        'phone_number'=>$request->phone_number,
                        'country_id'=>$request->country_id,
                        'company_id'=>$company->id,
                        'position_id'=>$request->position_id,
                        'user_id'=>$user->id,
                    ]);

                    return response()->json([
                        'status'=> true,
                        'message'=> 'User Account created successfully',
                        'user' => User::with('company')->find($user->id),
                        
                        
                    ], 200);
           
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            return response()->json([
                'status' => false,
                'error' => $exception->getMessage(),
                'message' => 'Registration failed',
            ], 200);
        }
    }


    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ], 200);
        } else {
            
            $user = User::find($request->user_id);
            if (Hash::check($request->code, $user->otp)) {
                //success
                $user->phone_verified_at = Carbon::now();
                $user->active = true;
                $user->otp = null;

                $token =  $user->createToken($user->id)->plainTextToken;

                if($user->save()) {
                    return response()->json([
                        'status' => true,
                        'token' => $token,
                        'message' => 'Verified successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'error' => 'Failed to verify, system error.',
                        'message' => 'System error'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'error' => 'Please check the verification code.',
                    'message' => 'Please check the verification code.'
                ], 401);
            }
        }
    }


    public function resetPassword(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required_without:email|exists:users,phone',
                'email' => 'required_without:phone|exists:users,email',
                'code' => 'required',
                'password' => 'required|min:8'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first()
                ], 200);
            } else {
                //get the user
                //get the user
                if($request->has('email')) {
                    $user = User::where('email', '=', $request->email)->first();
                } else {
                    $user = User::where('phone', '=', $request->phone)->first();
                }
                if ($user === null) {
                    return response()->json([
                        'status' => false,
                        'error' => 'User not found'
                    ], 200);
                }

                $passwordResetRecord = DB::table('password_resets')->where('email', '=', $user->email)->first();

                if (Hash::check($request->code, $passwordResetRecord->token)) {
                    //then it's validated
                    $user->password = Hash::make($request->password);

                    //remove the reset record
                    DB::table('password_resets')
                        ->where(
                            ['email' => $user->email],
                            ['token' => Hash::make($request->code)]
                        )->delete();

                    if ($user->save()) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Password reset successfully',
                            'user' => $user,
                           
                        ], 200);
                    }
                }

            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            return response()->json([
                'status' => false,
                'error' => 'Something went wrong.',
                'message' => 'Something went wrong'
            ], 401);
        }

        return response()->json([
            'status' => false,
            'error' => 'Something went wrong.',
            'message' => 'Something went wrong'
        ], 401);
    }


    public function forgotPassword(Request $request)
    
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required_without:email',
            'email' => 'required_without:phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first()
            ], 200);
        } else {
            //get the user
            if($request->has('email')) {
                $user = User::where('email', '=', $request->email)->first();
            } else {
                $user = User::where('phone_number', '=', $request->phone_number)->first();
            }

            if ($user===null) {
                return response()->json([
                    'status' => false,
                    'error' => 'User not found'
                ], 200);
            }

            //if we have the user, let's create the reset record using their email address
            //but first lets create a new OTP
            $otp = mt_rand(100000, 999999);

            DB::table('password_resets')
                ->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => Hash::make($otp)]
                );

            $user->notify(new PasswordResetRequested($user, $otp));

            return response()->json([
                'status' => true,
                'message' => 'Password reset initiated.',
                'user' => $user,
            ], 200);
        }

    }

}
