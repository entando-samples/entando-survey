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

Route::post('login', LoginController::class);
Route::post('reset', ResetPasswordController::class);
Route::post('/forget/password', ForgetPasswordController::class);
Route::post('logout', LogoutController::class)->middleware(['auth:sanctum']);
Route::post('/update/profile', UpdateProfileController::class)->middleware(['auth:sanctum']);

Route::group(['middleware' => [FakeSanctumAuth::class]], function () {
    Route::get('/my-configs', [Client\ConfigController::class, 'myConfigs']);
    Route::get('/message-topics', [Client\MessageTopicController::class, 'index']);
    Route::get('/messages', [Client\MessageController::class, 'index']);
    Route::post('/messages', [Client\MessageController::class, 'store']); //->middleware('throttle:20');
    Route::get('/messages/{id}', [Client\MessageController::class, 'show']);
    Route::get('/documents', [Client\DocumentController::class, 'index']);
    Route::get('/documents/{id}', [Client\DocumentController::class, 'show']);
    Route::apiResource('patient-folders', Client\PatientFolderController::class)
        ->except(['index', 'show']);
    Route::apiResource('patient-documents', Client\PatientDocumentController::class);
    Route::get('/surveys', [Client\SurveyController::class, 'index']);
    Route::get('/surveys/{id}', [Client\SurveyController::class, 'show']);
    Route::post('/surveys/{surveyId}/questions/{questionId}/answer', [Client\SurveyController::class, 'storeAnswer']);
});

Route::group(['prefix' => 'backend', 'middleware' => ['auth:sanctum', CheckBackendAccess::class]], function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::get('roles', fn () => success(User::roles()));
    Route::get('dashboard', DashboardController::class);

    Route::apiResource('users', Backend\UserController::class)
        ->only('index', 'show', 'store', 'update');

    Route::get('doctors', Backend\DoctorController::class);
    Route::get('documents-filter', Backend\DocumentFilterListingController::class);
    Route::get('pathologies/{pathology}/relation-items', [Backend\PathologyController::class, 'realtionItems']);
    Route::apiResource('pathologies', Backend\PathologyController::class);
    Route::apiResource('documents', Backend\DocumentController::class);
    Route::get('questions/filters', [Backend\QuestionController::class, 'filters']);
    Route::apiResource('questions', Backend\QuestionController::class)->except('update');
    Route::apiResource('surveys', Backend\SurveyController::class);
    Route::get('message-topics/{messageTopic}/messages-count', [Backend\MessageTopicController::class, 'messagesCount']);
    Route::apiResource('message-topics', Backend\MessageTopicController::class);
    Route::get('messages/inbound-count', [Backend\MessageController::class, 'inboundCount']);
    Route::post('messages/{message}/reply', [Backend\MessageController::class, 'reply']);
    Route::post('messages/{message}/mark-as-require-call', [Backend\MessageController::class, 'markAsRequireCall']);
    Route::post('messages/{message}/mark-as-called', [Backend\MessageController::class, 'markAsCalled']);
    Route::get("messages/configs", [Backend\MessageController::class, 'configs']);
    Route::get("messages/{listType}/list", [Backend\MessageController::class, 'index']);
    Route::apiResource("messages", Backend\MessageController::class)->except('index', 'update');
    Route::post('faq/sort', [Backend\FaqController::class, 'sort']);
    Route::apiResource("faq", Backend\FaqController::class);
    Route::get("credit", [Backend\CreditController::class, 'show']);
    Route::post("credit", [Backend\CreditController::class, 'update']);

    //assign docuement to patient
    Route::post('/patient/info-document', [Backend\DocumentController::class, 'assignDocumentToPatient']);
    Route::post('/patient/assign-survey', [Backend\SurveyController::class, 'assignSurveyToPatient']);
    //patients 
    Route::group(['prefix' => 'patients'], function () {
        Route::get('/', [Backend\PatientController::class, 'index']);
        Route::get("{patientId}/messages", [PatientController::class, 'messages']);
        Route::get('{patientId}/medical-data', [MedicalDataController::class, 'show']);
        Route::post('{patientId}/medical-data', [MedicalDataController::class, 'store']);
        Route::get('{patientId}/info-documents', [Backend\Patients\InfoDocumentController::class, 'index']);
        Route::post('{patientId}/info-documents/{documentId}/remind', [Backend\Patients\InfoDocumentController::class, 'remind']);
        Route::get('{patientId}/surveys', [Backend\Patients\SurveyController::class, 'index']);
        Route::get('{patientId}/surveys/{surveyId}', [Backend\Patients\SurveyController::class, 'show']);
        Route::post('{patientId}/surveys/{surveyId}/remind', [Backend\Patients\SurveyController::class, 'remind']);
        Route::post('{patientId}/surveys/{surveyId}/review', [Backend\Patients\SurveyController::class, 'review']);
        Route::get('documents/{documentId}/download', [Backend\Patients\DocumentController::class, 'download']);
        Route::post('documents/{document}/mark-as-read', [Backend\Patients\DocumentController::class, 'markAsRead']);
        Route::get('{id}/documents', [Backend\Patients\DocumentController::class, 'index']);
        Route::get('{patientId}/info', [PatientController::class, 'patientData']);
    });
});

Route::get('stream', [UploadController::class, 'stream']);
Route::post('uploads', [UploadController::class, 'store']);
Route::delete('uploads', [UploadController::class, 'destroy']);
Route::get('faqs', [Client\FaqController::class, 'index']);
Route::get("credit", [Backend\CreditController::class, 'show']);
