<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Option;
use App\Models\Pathology;
use App\Models\Question;
use App\Models\Survey;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Survey::count() < 20) {
            Survey::factory()
                ->count(10)
                ->afterCreating(function (Survey $survey) {
                    $questions = Question::inRandomOrder()->limit(3)->pluck('id')->toArray();
                    $survey->questions()->attach($questions);

                    $warnAnswers = Answer::whereIn('question_id', $questions)
                        ->inRandomOrder()
                        ->limit(random_int(0, 10))
                        ->pluck('id')
                        ->toArray();

                    $survey->warningAnswers()->attach($warnAnswers);
                })
                ->create();
        }
    }
}
