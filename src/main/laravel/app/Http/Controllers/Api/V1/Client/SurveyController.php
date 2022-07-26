<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyListResource;
use App\Models\PatientAnswer;
use App\Models\PatientSurveyPivot;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        $survey = Survey::
            findOrFail($id);

        $survey->load(['questions', 'warningAnswers','questions.answers']);

        return success($survey);

    }

    public function storeSingleAnswer(Request $request, $surveyId, $questionId)
    {

        $request->validate([
            'answer'=>'required|numeric|exists:answers,id',
        ]);

        $survey = Survey::find($surveyId);
        if(!$survey) {
            return response()->json(['message'=>'Survey not found'],404);
        }

        $patient = auth()->user();

        $surveyExists = PatientSurveyPivot::where('patient_id', $patient['token']->sub)
            ->where('survey_id',$surveyId)
            ->count();
        if ($surveyExists) {
            $answerExists = PatientAnswer::where('patient_id', $patient['token']->sub)
                ->where('survey_id',$surveyId)
                ->where('question_id', $questionId)
                ->count();
            if ($answerExists) {
                return response()->json(['message'=>'Answer already saved'], 409);
            }
        } else {
            PatientSurveyPivot::create([
                'patient_id'=>$patient['token']->sub,
                'survey_id'=>$surveyId
            ]);
        }

        PatientAnswer::create([
            'survey_id'=>$surveyId,
            'question_id'=>$questionId,
            'answer_id'=>$request->answer,
            'patient_id'=>$patient['token']->sub
        ]);

        return response()->json(['message'=>"Answer saved successfully"]);
    }
}
