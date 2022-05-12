<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'initial_date',
        'pathology_id',
        'doctor_id',
        'patient_id',
    ];

    public function pathology()
    {
        return $this->belongsTo(Pathology::class, 'pathology_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
