<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyListResource;
use App\Models\PatientAnswer;
use App\Models\PatientSurveyPivot;
use App\Models\Survey;
use Illuminate\Http\Request;

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

    public function storeAnswer(Request $request,$surveyId)
    {

        $request->validate([
            'value.*.question_id'=>'required|numeric|exists:questions,id',
            'value.*.answer_id'=>'required|numeric|exists:answers,id',
        ]);

        $survey = Survey::find($surveyId);
        if(!$survey):
            return response()->json(['message'=>'Survey not found'],404);
        endif;

        $patient = auth()->user();

        $surveyCompleted = PatientSurveyPivot::where('patient_id',$patient['token']->email)
            ->where('survey_id',$surveyId)
            ->count();
        if ($surveyCompleted){
            return response()->json(['message'=>'Survey already completed'],409);
        }


        PatientSurveyPivot::create([
            'patient_id'=>$patient['token']->email,
            'survey_id'=>$surveyId
        ]);

        foreach ($request->value as $answers):

            PatientAnswer::create([
                'survey_id'=>$surveyId,
                'question_id'=>$answers['question_id'],
                'answer_id'=>$answers['answer_id'],
                'patient_id'=>$patient['token']->email
            ]);
        endforeach;

        return response()->json(['message'=>"Answer saved successfully"]);
    }
}
