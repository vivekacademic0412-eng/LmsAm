<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\{LeadRegistrationController,LabSetupFormController,ContactApiController};
use Illuminate\Support\Facades\Route;
Route::post('/landing/register', LeadRegistrationController::class)->middleware('throttle:5,1');
Route::post('/contact-us', ContactApiController::class)->middleware('throttle:5,1');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('lab-setup-forms')->group(function () {
    Route::get('/', [LabSetupFormController::class, 'index']);
    Route::post('/', [LabSetupFormController::class, 'store']);
    Route::get('/{labSetupForm}', [LabSetupFormController::class, 'show']);
});