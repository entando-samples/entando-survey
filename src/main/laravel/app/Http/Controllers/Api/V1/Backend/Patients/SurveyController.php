<?php

namespace App\Http\Controllers\Api\V1\Backend\Patients;

use App\Http\Controllers\Controller;
use App\Jobs\RemindSurvey;
use App\Models\PatientSurveyPivot;
use App\Models\Survey;
use App\Models\User;

class SurveyController extends Controller
{
    public function index($id)
    {
        $patient = User::patient()->findOrFail($id);

        $documents = $patient
            ->surveys()
            ->with(
                'reminders',
                // only reminders for this patient
                fn ($q) => $q->where('patient_survey.patient_id', $patient->id)->with('user:id,name')->limit(2)
            )
            ->select(['surveys.id', 'surveys.title', 'surveys.is_alertable'])
            // INFO: this is doing sub query which can slow down the entire query (might need refactor later)
            ->withCount(['reminders' => fn ($q) => $q->where('patient_survey.patient_id', $patient->id)])
            ->get();

        return success($documents);
    }

    public function show($patientId, $surveyId)
    {
        $patient = User::findOrFail($patientId);

        $survey = $patient->surveys()
            ->with([
                'patientAnswers' => fn ($q) => $q->select(['answers.id', 'answers.body', 'answers.question_id'])
                    ->with('question:id,title,description'),
                'warningAnswers:id'
            ])
            ->findOrFail($surveyId);

        return success($survey);
    }

    public function remind($patientId, $surveyId)
    {
        $survey = Survey::findOrFail($surveyId);

        $patient = User::patient()->findOrFail($patientId);

        $pivot = PatientSurveyPivot::where(['patient_id' => $patientId, 'survey_id' => $surveyId])->firstOrFail();

        if ($pivot->completed_at) return problem("Survey is already completed");

        $pivot->reminders()->forceCreate([
            'pivot_id' => $pivot->id,
            'reminded_by' => auth()->id()
        ]);

        RemindSurvey::dispatch($patient, $survey);

        return success();
    }

    public function review($patientId, $surveyId)
    {
        $pivot = PatientSurveyPivot::where(['patient_id' => $patientId, 'survey_id' => $surveyId])->firstOrFail();

        if (!$pivot->completed_at) problem("Survey is not completed yet");

        $pivot->update([
            'reviewed_at' => $pivot->reviewed_at ?? now(),
            'note' => request('note', '')
        ]);

        return success($pivot);
    }
}
