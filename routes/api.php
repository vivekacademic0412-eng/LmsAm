<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\LeadRegistrationController;
use Illuminate\Support\Facades\Route;
Route::post('/landing/register', LeadRegistrationController::class)->middleware('throttle:5,1');;
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});