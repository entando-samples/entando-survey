<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Message;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $survey = $request->filterBySurvey;
        $document = $request->filterByDocument;
        $infodocument = $request->infoDocument;
        $filterSurvey = $request->filterSurvey;

        $patients = User::patient()
            ->with(['surveys', 'documents'])
            ->search($request->search)
            ->whereInPathologies($request->pathologies)
            ->whereHasDoctors($request->doctors)
            ->when(request('forListing') == "true", fn ($q) => $q->withCount(
                // INFO: Can be optimized
                [
                    'documents',
                    'documents as read_documents_count' => fn ($q) => $q->where('document_patient.is_read', true),
                    'surveys',
                    'surveys as read_surveys_count' => fn ($q) => $q->where('patient_survey.completed_at', '!=', null),
                ]
            ));

        if (isset($survey)) {
            $patients->whereHas('surveys', function ($q) use ($survey) {
                return $q->where('surveys.id', $survey);
            });
        }

        if (isset($infodocument)) {
            if ($infodocument == "unread") {
                $patients->whereHas('documents', function ($query) {
                    return $query->where('is_read', 0);
                });
            }
        }
        // if (isset($filterSurvey)) {
        //     if ($filterSurvey == "alertable") {

        //     }
        // }

        if (isset($document)) {
            $patients->whereHas('documents', function ($q) use ($document) {
                return $q->where('documents.id', $document);
            });
        }
        return success($patients->paginate());
    }

    public function messages($patientId)
    {
        $patient = User::patient()->findOrFail($patientId);

        $messages = Message::withoutGlobalScope('not-archived')
            ->where('receiver_id', $patient->id)
            ->where('from_bo', true)
            ->get(['id', 'body', 'created_at', 'read_at', 'topic_id']);

        return success($messages);
    }

    public function patientData($id)
    {
        $user = User::userData($id)->firstOrFail(['name']);
        return success($user);
    }
}
