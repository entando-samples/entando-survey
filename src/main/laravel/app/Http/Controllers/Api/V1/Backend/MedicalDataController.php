<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Models\MedicalData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicalDataController extends Controller
{
    public function store(Request $request, $patientId)
    {
        $attributes = $request->validate([
            'doctor' => ['required', 'integer', Rule::exists('users', 'id')->where('role', User::DOCTOR)],
            'pathology' => ['required', 'integer', Rule::exists('pathologies', 'id')],
        ]);

        $patient = User::patient()
            ->with('medicalData')
            ->findOrFail($patientId);

        if ($patient->medicalData) return problem("Patient already has information", 400);

        $patient->medicalData()->create([
            'doctor_id' => $attributes['doctor'],
            'pathology_id' => $attributes['pathology'],
            'initial_date' => now(),
        ]);

        return success();
    }

    public function show($patientId)
    {
        $data = MedicalData::where('patient_id', $patientId)
            ->with(['doctor:id,name,email', 'pathology:id,title'])
            ->select(['id', 'initial_date', 'doctor_id', 'pathology_id'])
            ->first();

        return success($data);
    }
}
