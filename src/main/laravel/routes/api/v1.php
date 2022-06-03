<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Client;
use App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Api\V1\Backend\DashboardController;
use App\Http\Middleware\FakeSanctumAuth;
use App\Http\Middleware\CheckBackendAccess;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\LogoutController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\Backend\MedicalDataController;
use App\Http\Controllers\Api\V1\Backend\PatientController;
use App\Http\Controllers\Api\V1\Backend\UserController;
use App\Http\Controllers\Api\V1\ForgetPasswordController;
use App\Http\Controllers\Api\V1\ResetPasswordController;
use App\Http\Controllers\Api\V1\UpdateProfileController;
use App\Models\User;

Route::get('/test',function(){
   return auth()->user()->token;
});


// Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/surveys', [Client\SurveyController::class, 'index']);
    Route::get('/surveys/{id}', [Client\SurveyController::class, 'show']);
    Route::post('/surveys/{surveyId}/questions/{questionId}/answer', [Client\SurveyController::class, 'storeAnswer']);
// });

Route::group(['prefix' => 'backend', 'middleware' => 'auth:api'], function () {
    Route::get('/me', [UserController::class, 'me']);

    Route::get('questions/filters', [Backend\QuestionController::class, 'filters']);
    Route::apiResource('questions', Backend\QuestionController::class)->except('update');
    Route::apiResource('surveys', Backend\SurveyController::class);
});
