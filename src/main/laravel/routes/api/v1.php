<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Client;
use App\Http\Controllers\Api\V1\Backend;
use App\Http\Controllers\Api\V1\Backend\UserController;

Route::get('/test',function(){

//    return config('keycloak.realm_public_key');
   return auth()->user();
});


 Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/surveys', [Client\SurveyController::class, 'index']);
    Route::get('/surveys/{id}', [Client\SurveyController::class, 'show']);
    Route::post('/surveys/{surveyId}/questions/{questionId}/answer', [Client\SurveyController::class, 'storeAnswer']);
 });

Route::group(['prefix' => 'backend', 'middleware' => 'auth:api'], function () {
    Route::get('/me', [UserController::class, 'me']);

    Route::get('questions/filters', [Backend\QuestionController::class, 'filters']);
    Route::apiResource('questions', Backend\QuestionController::class)->except('update');
    Route::apiResource('surveys', Backend\SurveyController::class);
});
