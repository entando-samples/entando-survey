<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'patient_id',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDocument::class, 'folder_id');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->patient_id == $user->id;
    }
}
