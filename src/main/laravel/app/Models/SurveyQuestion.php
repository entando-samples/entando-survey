<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SurveyQuestion extends Pivot
{
    use HasFactory;

    protected $table = 'question_survey';

    // public function warningAnswers()
    // {
    //     return $this->hasMany()
    // }
}
