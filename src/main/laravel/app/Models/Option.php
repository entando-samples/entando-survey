<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Option extends Pivot
{
    use HasFactory;

    protected $table = 'answer_question';
}
