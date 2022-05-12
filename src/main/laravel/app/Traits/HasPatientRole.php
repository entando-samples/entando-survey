<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Survey;
use App\Models\Document;
use App\Models\PatientFolder;
use App\Models\PatientDocument;
use App\Models\PatientSurveyPivot;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentPatientPivot;
use App\Models\MedicalData;
use App\Models\Pathology;
use App\Models\PatientAnswer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasPatientRole
{
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_patient', 'patient_id', 'document_id')
            ->using(DocumentPatientPivot::class)
            ->orderBy('document_patient.created_at')
            ->withPivot(['id', 'is_read','send_via'])
            ->withTimestamps();
    }

    public function folders(): HasMany
    {
        return $this->hasMany(PatientFolder::class, 'patient_id');
    }

    public function patient_documents(): HasManyThrough
    {
        return $this->hasManyThrough(PatientDocument::class, PatientFolder::class, 'patient_id', 'folder_id');
    }

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

    public function medicalData()
    {
        return $this->hasOne(MedicalData::class, 'patient_id', 'id');
    }

    public function doctor()
    {
        return $this->hasOneThrough(User::class, MedicalData::class, 'patient_id', 'id', 'id', 'doctor_id');
    }

    public function pathology()
    {
        return $this->hasOneThrough(Pathology::class, MedicalData::class, 'patient_id', 'id', 'id', 'pathology_id');
    }

    public function scopePatient(Builder $q)
    {
        return $q->where("role", User::PATIENT);
    }

    public function markDocumentAsRead(Document $document)
    {
        DB::table('document_patient')
            ->where('patient_id', $this->id)
            ->where('document_id', $document->id)
            ->update(['is_read' => 1, 'updated_at' => now()]);
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
