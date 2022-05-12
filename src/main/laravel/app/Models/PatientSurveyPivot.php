<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientSurveyPivot extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    protected $table = 'patient_survey';

    public function reminders(): HasMany
    {
        return $this->hasMany(SurveyReminder::class, 'pivot_id', 'id');
    }

    public function scopeInCompleted($query)
    {
        $query->whereNull('completed_at');
    }

    public function scopeNotReviewed($q)
    {
        $q->whereNull('reviewed_at');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }
}
