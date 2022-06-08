<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Survey;

use App\Models\PatientSurveyPivot;
use App\Models\PatientAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPatientRole
{


    public function surveys(): BelongsToMany
    {
        return $this->belongsToMany(Survey::class, 'patient_survey', 'patient_id', 'survey_id')
            ->using(PatientSurveyPivot::class)
            ->withPivot(['id', 'completed_at', 'reviewed_at', 'note','send_via'])
            ->withTimestamps();
    }

    public function surveyAns(): HasMany
    {
        return $this->hasMany(PatientAnswer::class, 'patient_id');
    }

    public function scopePatient(Builder $q)
    {
        return $q->where("role", User::PATIENT);
    }

    public function scopeWhereInPathologies($q, $ids)
    {
        if (!$ids) return $q;

        if (!is_array($ids)) $ids = [$ids];

        if (empty($ids)) return $q;

        return $q->whereHas('pathology', fn ($sq) => $sq->whereIn('pathologies.id', $ids));
    }

    public function scopeWhereHasDoctors($q, $ids)
    {
        if (!$ids) return $q;

        if (!is_array($ids)) $ids = [$ids];

        if (empty($ids)) return $q;

        return $q->whereHas('doctor', fn ($sq) => $sq->whereIn('medical_data.doctor_id', $ids));
    }

    public static function getPatientData(User $user)
    {
        return $user->only(['name', 'email']);
    }
}
