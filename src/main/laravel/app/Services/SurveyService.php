<?php

namespace App\Services;

use App\Exceptions\AlreadyExist;
use App\Jobs\AlertableSurveyReminderJob;
use App\Models\PatientAnswer;
use App\Models\PatientSurveyPivot;
use App\Models\Survey;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SurveyService
{
    public function checkIfExist($surveyId, $questionId, $patientId)
    {
        if (PatientAnswer::query()
            ->where([
                "survey_id" => $surveyId,
                "patient_id" => $patientId ?? auth()->id(),
                "question_id" => $questionId,
            ])
            ->exists()
        ) {
            throw new AlreadyExist("Answer already exists for question");
        }
    }

    public function markSurveyCompleted($surveyId, $patient)
    {
        $tsq = Survey::find($surveyId)->questions()->count();
        $patientAnswers = $patient->surveyAns()->where('survey_id', $surveyId)->pluck('answer_id')->toArray();
        $surveyWarning = Survey::findOrFail($surveyId)->warningAnswers()->pluck('answer_id')->toArray();
        if ($tsq === count($patientAnswers)) {
            $patient->surveys()
                ->updateExistingPivot($surveyId, array('completed_at' => Carbon::now()), true);
            //if survey has aleartable answer selection
            if (array_intersect($patientAnswers, $surveyWarning)) {
                //patients consulting doctor
                $doctor = $patient->doctor()->first();
                dispatch(new AlertableSurveyReminderJob($doctor, $patient, $surveyId));
            }
        }
    }

    public function incompletedSurvey()
    {
        return Survey::query()->whereHas('patients')->whereDoesntHave('patientAnswers')->count();
    }


    public function partiallyCompletedSurvey()
    {
        $partialIncompleteSurvey = PatientSurveyPivot::query()
            ->select(['patient_survey.patient_id', 'patient_survey.survey_id'])
            ->whereNull('patient_survey.completed_at')
            ->join('patient_answers', function ($join) {
                $join->on('patient_answers.survey_id', '=', 'patient_survey.survey_id');
                $join->on('patient_answers.patient_id', '=', 'patient_survey.patient_id');
            })
            ->count();
        return $partialIncompleteSurvey;
    }


    //total alertable survey done by patient
    public function totalAlertableSurvey()
    {
        $alertableAnswers = DB::table('survey_warning_ans')->pluck('answer_id');
        return PatientAnswer::whereIn('answer_id', $alertableAnswers)
            ->distinct(['patient_answers.survey_id', 'patient_answers.patient_id'])
            ->join('patient_survey', function ($join) {
                $join->on('patient_answers.survey_id', '=', 'patient_survey.survey_id');
                $join->on('patient_answers.patient_id', '=', 'patient_survey.patient_id');
            })
            ->whereNull('patient_survey.reviewed_at')
            ->count();
    }
}
