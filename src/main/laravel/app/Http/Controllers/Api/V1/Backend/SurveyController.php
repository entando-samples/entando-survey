<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignSurveyPatientRequest;
use App\Http\Requests\Backend\SurveyRequest;
use App\Jobs\RemindSurvey;
use App\Models\Answer;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys =  Survey::query()
            ->latest()
            ->search(request('search'))
            ->whereInPathologies(request('pathologies'))
            ->select(['id', 'title'])
            ->with(['pathologies:id,title'])
            ->withCount(['patients'])
            ->paginate()
            ->through(function ($item) {
                $item->can_be_edited =  $item->canBeEdited();
                return $item;
            });
        // dd($surveys);

        return success($surveys);
    }

    public function store(SurveyRequest $request)
    {
        $attributes = $request->validated();

        $survey = DB::transaction(function () use ($attributes) {
            $survey = Survey::query()->create($attributes);

            if (isset($attributes['pathologies'])) {
                $survey->pathologies()->sync($attributes['pathologies']);
            }

            $survey->questions()->sync($attributes['questions']);

            if (isset($attributes['warning_answers'])) {
                $survey->warningAnswers()->sync(
                    Answer::whereIn('question_id', $attributes['questions'])
                        ->whereIn('id', $attributes['warning_answers'])
                        ->pluck('id')
                        ->toArray()
                );
            }

            return $survey;
        });

        return success($survey, "Survey saved successfully");
    }

    public function show(Survey $survey)
    {
        $survey->load(['pathologies', 'questions', 'warningAnswers']);

        return success($survey);
    }

    public function update(SurveyRequest $request, Survey $survey)
    {
        if (!$survey->canBeEdited()) return problem(null, "Survey can't be updated");

        $attributes = $request->validated();

        $survey =  DB::transaction(function () use ($survey, $attributes) {
            $survey->update($attributes);

            if (isset($attributes['pathologies'])) {
                $survey->pathologies()->sync($attributes['pathologies']);
            }

            if (isset($attributes['questions'])) {
                $survey->questions()->sync($attributes['questions']);
            }


            if (isset($attributes['warning_answers'])) {
                $survey->warningAnswers()->sync(
                    Answer::whereIn('question_id', $attributes['questions'])
                        ->whereIn('id', $attributes['warning_answers'])
                        ->pluck('id')
                        ->toArray()
                );
            }

            return $survey;
        });

        return success($survey, "Survey update successfully");
    }

    public function destroy(Survey $survey)
    {
        DB::transaction(function () use ($survey) {
            $survey->pathologies()->sync([]);
            $survey->questions()->sync([]);
            $survey->warningAnswers()->sync([]);

            $survey->delete();
        });

        return success(null, "Survey deleted successfully");
    }

    public function assignSurveyToPatient(AssignSurveyPatientRequest $request)
    {
        $data = $request->validated();
        $patient = User::findOrFail($data['patient_id']);
        $patient->surveys()->attach(
            [
                'survey_id' => $data['survey_id'],
            ],
            [
                'send_via' => 'DIRECT'
            ]
        );
        $survey = Survey::find($data['survey_id']);
        $notificationTitle = "New survey";
        RemindSurvey::dispatch($patient,$survey,$notificationTitle);

        return success([], "Survey assinged to patient successfull.");
    }
}
