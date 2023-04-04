<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route::middleware('auth:api')->group(function(){
    Route::get('all-customers',[AuthController::class,'getCustomers']);
    Route::post('update/customers/{id}',[AuthController::class,'updateCustomers']);
// });