<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\LeadRegistrationController;
use Illuminate\Support\Facades\Route;
Route::post('/landing/register', LeadRegistrationController::class);