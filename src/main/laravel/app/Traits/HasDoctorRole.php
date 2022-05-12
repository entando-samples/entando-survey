<?php

namespace App\Traits;

use App\Models\MedicalData;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use phpDocumentor\Reflection\Types\Boolean;

trait HasDoctorRole
{
    public function scopeDoctorRole(Builder $q)
    {
        return $q->where("role", User::DOCTOR);
    }

    /**
     * Get all of the patients for the HasDoctorRole
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patients()
    {
        return $this->where('role', User::PATIENT)->whereHas('medicalData', function ($query) {
            $query->where('doctor_id', $this->id);
        });
    }
}
