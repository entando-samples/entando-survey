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
    Route::apiResource('surveys', Backend\SurveyController::class)->except([
      'create', 'store', 'update', 'destroy'
    ]);
    Route::apiResource('questions', Backend\QuestionController::class)->except([
      'create', 'store', 'update', 'destroy'
    ]);
    Route::get('questions/filters', [Backend\QuestionController::class, 'filters']);
    Route::get('/my-surveys',[Client\SurveyController::class,'index']);
    Route::post('/surveys/response/{surveyId}', [Client\SurveyController::class, 'storeAnswers']);
    Route::post('/surveys/{surveyId}/questions/{questionId}/answer', [Client\SurveyController::class, 'storeSingleAnswer']);
 });

Route::group(['prefix' => 'backend', 'middleware' => ['auth:api', 'surveyrole']], function () {
    Route::get('/me', [UserController::class, 'me']);

    Route::get('questions/filters', [Backend\QuestionController::class, 'filters']);
    Route::apiResource('questions', Backend\QuestionController::class)->except('update');
    Route::apiResource('surveys', Backend\SurveyController::class);
});
