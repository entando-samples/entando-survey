<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Exceptions\AlreadyExist;
use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyListResource;
use App\Http\Resources\SurveyResource;
use App\Models\PatientAnswer;
use App\Models\Survey;
use App\Services\SurveyService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $data = $user->surveys()
            ->orderBy('patient_survey.created_at', 'DESC')
            ->get(['surveys.id', 'surveys.title']);

        return SurveyListResource::collection($data);
    }

    public function show($id)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $survey = $user->surveys()
            ->findOrFail($id);

        $survey->load([
            'questions' => fn ($q) => $q
                ->with(['answers'])
                ->select(['questions.id', 'questions.description as question'])
        ]);

        $patientAnswers = $survey
            ->patientAnswers()
            ->where('patient_id', auth()->id())
            ->pluck('patient_answers.answer_id', 'patient_answers.question_id');

        $survey->questions->each(fn ($question) => $question->patient_answer = $patientAnswers[$question->id] ?? null);

        return SurveyResource::make($survey);
    }

    public function storeAnswer($surveyId, $questionId, SurveyService $surveyService)
    {
        /** @var \App\Models\User */
        $patient = auth()->user();

        $survey = $patient->surveys()
            ->whereHas('questions', fn ($q) => $q->where('questions.id', $questionId))
            ->findOrFail($surveyId);


        //validate question_id
        request()->validate([
            'answer' => [
                'required',
                'numeric',
                Rule::exists('answers', 'id')->where('question_id', $questionId)
            ]
        ]);


        $surveyService->checkIfExist($surveyId, $questionId, $patient->id);

        DB::transaction(function () use ($survey, $patient, $questionId) {
            $survey->patientAnswers()->attach([
                request('answer') => [
                    'patient_id' => $patient->id,
                    'question_id' => $questionId,
                ]
            ]);

            $isWarnableAnswer = $survey->warningAnswers()
                ->where("answer_id", request('answer'))
                ->exists();

            if (!$isWarnableAnswer) return;

            if (!$patient->is_alertable) {
                $patient->update([
                    'is_alertable' => true,
                ]);
            }

            if (!$survey->is_alertable) {
                $survey->update([
                    'is_alertable' => true,
                ]);
            }
        }, 2);

        $surveyService->markSurveyCompleted($surveyId, $patient);

        return success("Answer saved successfully");
    }
}
