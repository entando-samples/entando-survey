<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DocumentPatientPivot extends Pivot
{
    public $incrementing = true;

    protected $table = 'document_patient';

    public function reminders(): HasMany
    {
        return $this->hasMany(DocumentReminder::class, 'pivot_id', 'id');
    }
}
