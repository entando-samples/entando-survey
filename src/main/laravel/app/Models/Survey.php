<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Survey extends Model
{
    use HasFactory, HasEagerLimit;

    protected $fillable = [
        'title',
        'description',
        'is_alertable',
    ];

    protected $casts = [
        'is_alertable' => 'boolean',
    ];



    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_survey');
    }

    public function warningAnswers(): BelongsToMany
    {
        return $this->belongsToMany(Answer::class, 'survey_warning_ans', 'survey_id', 'answer_id');
    }


    /**
     * The patient that belong to the Survey
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'patient_survey','survey_id','patient_id')->withPivot(PatientSurveyPivot::class);
    }

    public function patientAnswers(): BelongsToMany
    {
        return $this->belongsToMany(Answer::class, 'patient_answers', 'survey_id', 'answer_id')
            ->using(PatientAnswer::class)
            ->withPivot('question_id', 'patient_id');
    }

    public function reminders()
    {
        return $this->hasManyThrough(SurveyReminder::class, PatientSurveyPivot::class, 'survey_id', 'pivot_id')
            ->latest();
    }

    public function canBeEdited()
    {
        return true;
    }

    public function scopeSearch($q, $value)
    {
        if (!$value) return $q;

        $value = "%{$value}%";

        return $q->where('title', 'like', $value);
    }


}
