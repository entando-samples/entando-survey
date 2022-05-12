<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Models\Document;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssingDocuemtPatientRequest;
use App\Http\Requests\Backend\DocumentRequest;
use App\Jobs\RemindInfoDocument;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents =   Document::with(['creator:id,name', 'pathologies:id,title'])
            ->select(['id', 'title', 'created_at', 'created_by'])
            ->search(request('search'))
            ->whereInPathologies(request('pathologies'))
            ->withCount('patients')
            ->latest()
            ->paginate();

        return success($documents);
    }

    public function store(DocumentRequest $request)
    {
        $attributes = $request->validated();

        $attributes['created_by'] = auth()->id();

        $document = DB::transaction(function () use ($attributes) {
            $document = Document::create($attributes);

            if (isset($attributes['pathologies'])) {
                $document->pathologies()->sync($attributes['pathologies']);
            }

            return $document;
        });

        return success($document, "Document saved successfully");
    }

    public function show(Document $document)
    {
        $document->load(['creator:id,name', 'lastUpdator:id,name', 'pathologies']);

        return success($document);
    }

    public function update(DocumentRequest $request, Document $document)
    {
        $attributes = $request->validated();

        $attributes['updated_by'] = auth()->id();

        $document = DB::transaction(function () use ($document, $attributes) {
            $document->update($attributes);

            if (isset($attributes['pathologies'])) {
                $document->pathologies()->sync($attributes['pathologies']);
            }

            return $document;
        });

        return success(null, 'Document updated successfully');
    }

    public function destroy(Document $document)
    {
        DB::transaction(function () use ($document) {
            $document->pathologies()->sync([]);

            $document->delete();
        });
        return success(null, 'Document deleted successfully');
    }

    public function assignDocumentToPatient(AssingDocuemtPatientRequest $request)
    {
        $data = $request->validated();
        $patient = User::findOrFail($data['patient_id']);
        $patient->documents()->attach([
            'document_id' => $data['document_id'],
        ], [
            'send_via' => 'DIRECT',
        ]);
        $document =  Document::find($data['document_id']);
        $notificationTitle = "New Document";
        RemindInfoDocument::dispatch($patient, $document,$notificationTitle);
 
        return success([], "Document assinged to patient successfully.");
    }
}
