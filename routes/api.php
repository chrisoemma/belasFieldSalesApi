<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\Taskcontroller;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\PunchRecordController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'loginByPhone']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('verify-phone', [AuthController::class, 'verifyPhone']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::group(['prefix' => 'companies'], function () {
     Route::get('employees/{company_id}',[CompanyController::class,'employees']);
     Route::get('clients/{company_id}',[CompanyController::class,'company_clients']);
    });

     Route::group(['prefix' => 'check_ins'], function () {
                 
              
     Route::post('create_check_in/{user_id}',[CheckInController::class,'store']);
     Route::get('checks_ins_outs_by_user/{request_type}/{user_id}',[CheckInController::class,'checks_ins_outs_by_user']);
     Route::get('/company_checkIns/{user_id}',[CheckInController::class,'check_ins_outs_by_company']);
     Route::put('/update_check_ins/{check_in_id}',[CheckInController::class,'update_checkIns']);
     Route::post('/check_out/{check_in_id}',[CheckOutController::class,'check_out']);
    });

    Route::group(['prefix' => 'clients'], function () {
     Route::get('company/{company_id}',[ClientController::class,'clients']);
     Route::post('create_company_client/{company_id}',[ClientController::class,'store_company_client']); 
     Route::put('update_company_client/{client_id}',[ClientController::class,'update_company_client']);
     Route::post('add_contact_person/{client_id}',[ClientController::class,'add_contact_person']);  
     Route::put('update_contact_person/{client_id}/{contact_person_id}',[ClientController::class,'update_contact_person']);
     Route::get('contact_people/{client_id}',[ClientController::class,'contact_people']);    
    });

    Route::group(['prefix' => 'tasks'], function () {
        Route::get('my_assigned_tasks/{status}/{id}',[TaskController::class,'my_assigned_tasks']);
        Route::get('get_my_assign_tasks/{status}/{id}',[TaskController::class,'get_my_assign_tasks']);
        Route::put('task_status_update/{id}',[TaskController::class,'update_task_status']);
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('sales_people/{company_id}/{user_id}',[Usercontroller::class,'sales_people']);
    });

    Route::resource('users', Usercontroller::class);
    Route::resource('companies', CompanyController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('industries', IndustryController::class);

    Route::group(['prefix' => 'punch_records'], function () {
        Route::post('punch_in/{user_id}',[PunchRecordController::class,'punchIn']);
        Route::post('punch_out/{user_id}',[PunchRecordController::class,'punchOut']);
        Route::get('today_punch_records/{user_id}',[PunchRecordController::class,'getTodayPunchRecords']);
        Route::post('get_user_punch_records_by_date_range/{user_id}',[PunchRecordController::class,'getPunchRecordsByDateRangeUsers']);
    });
});
