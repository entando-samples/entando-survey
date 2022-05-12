<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Throwable;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = json_decode(file_get_contents(base_path('questions.json')), true);

        foreach ($questions as $question) {
            try {
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

                foreach ($question['localizedAnswers'][0]['answers'] as $answer) {
                    $model->answers()->create(['body' => $answer]);
                }
            } catch (Throwable $th) {
                throw $th;
            }
        }
    }
}
