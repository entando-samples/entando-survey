<?php

namespace App\Http\Controllers\Api\V1\Backend\Patients;

use App\Http\Controllers\Controller;
use App\Jobs\RemindInfoDocument;
use App\Models\Document;
use App\Models\DocumentPatientPivot;
use App\Models\User;

class InfoDocumentController extends Controller
{
    public function index($patientId)
    {
        $patient = User::patient()->findOrFail($patientId);

        $documents = $patient
            ->documents()
            ->with(
                'reminders',
                // only reminders for this patient
                fn ($q) => $q->where('document_patient.patient_id', $patient->id)
                    ->with('user:id,name')->limit(2)
            )
            ->select(['documents.id', 'documents.title'])
            // INFO: this is doing sub query which can slow down the entire query (might need refactor later)
            ->withCount(['reminders' => fn ($q) => $q->where('document_patient.patient_id', $patient->id)])
            ->get();

        return success($documents);
    }

    public function remind($patientId, $documentId)
    {
        $document = Document::findOrFail($documentId);

        $patient = User::patient()->findOrFail($patientId);

        $pivot = DocumentPatientPivot::query()
            ->where(["patient_id" => $patientId, "document_id" => $documentId])
            ->firstOrFail();

        if ($pivot->is_read == 1) return problem("Document is already read");

        $pivot->reminders()->forceCreate([
            'pivot_id' => $pivot->id,
            'reminded_by' => auth()->id()
        ]);

        RemindInfoDocument::dispatch($patient, $document);

        return success();
    }
}
