<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Exceptions\AlreadyExist;
use App\Http\Controllers\Controller;
use App\Http\Resources\SurveyListResource;
use App\Http\Resources\SurveyResource;
use App\Models\PatientAnswer;
use App\Models\PatientSurveyPivot;
use App\Models\Survey;
use App\Services\SurveyService;
use Carbon\Carbon;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

        $survey->load(['pathologies', 'questions', 'warningAnswers','questions.answers']);

        return success($survey);

//        $survey->load([
//            'questions' => fn ($q) => $q
//                ->with(['answers'])
//                ->select(['questions.id', 'questions.description as question'])
//        ]);



//        $survey->questions->each(fn ($question) => $question->patient_answer = $patientAnswers[$question->id] ?? null);

//        return SurveyResource::make($survey);
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




        // $surveyService->checkIfExist($surveyId, $questionId, $patient->id);

        // DB::transaction(function () use ($survey, $patient, $questionId) {
        //     $survey->patientAnswers()->attach([
        //         request('answer') => [
        //             'patient_id' => $patient->id,
        //             'question_id' => $questionId,
        //         ]
        //     ]);

        // }, 2);

        return response()->json(['message'=>"Answer saved successfully"]);
    }
}
