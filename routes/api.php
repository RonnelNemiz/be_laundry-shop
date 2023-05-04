<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\API\AuthController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route::middleware('auth:api')->group(function(){
    // Route::get('all-customers',[AuthController::class,'getCustomers']);
    // Route::put('update/customers/{profile}',[ProfileController::class,'editProfile']);
    // Route::delete('delete/customers/{id}',[ProfileController::class,'destroy']);

    Route::get('roles', [RolesController::class, 'show']);
    Route::get('users', [UserController::class, 'show']);
    Route::post('add/users', [UserController::class, 'store']);
    Route::put('update/user/{user}', [UserController::class, 'editUser']);
    Route::delete('delete/user/{user}', [UserController::class, 'destroy']);

    Route::get('all-customers', [UserController::class,'getCustomers']);
    Route::post('add/customer', [UserController::class,'addCustomer']);
    Route::put('update/customer/{user}', [UserController::class,'editCustomer']);

    Route::get('services', [ServiceController::class,'index']);
    Route::post('add/services', [ServiceController::class,'store']);
    Route::put('update/service{service}', [ServiceController::class,'update']);
    Route::delete('delete/service{service}', [ServiceController::class,'destroy']);
// });