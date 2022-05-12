<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class PatientAnswer extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $table = 'patient_answers';

    public function alertAnswers(){
        return $this->hasOne(AlertableAnswer::class,'answer_id','answer_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class,'patient_id');
    }

}
