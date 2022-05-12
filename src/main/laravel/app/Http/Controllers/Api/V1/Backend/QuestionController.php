<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\QuestionRequest;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index()
    {
        $query = Question::query()
            ->search(request('search'))
            ->with('answers:id,body,question_id')
            ->whereInProtocols(request('protocols'))
            ->whereInQuestions(request('questions'))
            ->orderBy('order');

        if (request('paginated') === 'false') {
            $questions = $query->get();
        } else {
            $questions = $query->paginate();
        }

        return success(
            $questions
        );
    }

    public function store(QuestionRequest $request)
    {
        $attributes = $request->validated();

        try {
            DB::transaction(function () use ($attributes) {
                $question = json_decode($attributes['json'], true);

                $model = Question::updateOrCreate(
                    [
                        'key' => $question['primaryKey'],
                    ],
                    [
                        'protocol' => $question['protocol'],
                        'p_question' => $question['protocolQuestion'],
                        'title' => $question['localizedQuestion'][0]['question'],
                        'description' => $question['localizedQuestionDescription'][0]['description'],
                        'order' => $question['order'],
                    ]
                );

                $currentAnswers = [];
                foreach ($question['localizedAnswers'][0]['answers'] as $answer) {
                    $currentAnswers[] = $model->answers()->firstOrCreate(['body' => $answer])->id;
                }
                $model->answers()->whereNotIn('id', $currentAnswers)->delete();
            });
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'json' => "Invalid json"
            ]);
        }

        return success(null, "Question saved successfully");
    }

    public function show(Question $question)
    {
        $question->load('answers');
        return success($question);
    }

    public function destroy(Question $question)
    {
        DB::transaction(function ()  use ($question) {
            // $question->answers()->sync([]);
            $question->surveys()->sync([]);

            $question->delete();
        });

        return success(null, "Question deleted successfully");
    }

    public function filters()
    {
        return success([
            'questions' => Question::query()->distinct('p_question')->pluck('p_question'),
            'protocols' => Question::query()->distinct('protocol')->pluck('protocol'),
        ]);
    }
}
