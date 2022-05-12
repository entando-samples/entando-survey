<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\PatientDocument;
use App\Services\SurveyService;

class DashboardController extends Controller
{
    public function __invoke(SurveyService $surveyService)
    {

        return success([
            'documents' => [
                'total' => PatientDocument::count(),
                'read' => PatientDocument::whereNotNull('read_at')->count(),
            ],
            'surveys' => [
                'total' => $surveyService->incompletedSurvey(),
                'alertable' => $surveyService->totalAlertableSurvey(),
                'partially' => $surveyService->partiallyCompletedSurvey(),
            ],
            'infoDocuments' => [
                'total' => Document::query()->whereHas('patients')->count(),
                'read' => Document::query()->whereHas('patients', function ($query) {
                    $query->where('is_read', true);
                })->count(),
            ],
        ]);
    }
}
