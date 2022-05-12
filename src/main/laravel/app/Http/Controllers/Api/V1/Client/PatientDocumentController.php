<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientDocumentRequest;
use App\Http\Resources\PatientDocumentResource;
use App\Http\Resources\PatientFolderListResource;
use App\Models\PatientDocument;
use Illuminate\Support\Facades\Auth;

class PatientDocumentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $data = $user
            ->folders()
            ->with('documents:folder_id,id,title,created_at,updated_at')
            ->get();

        return PatientFolderListResource::collection($data);
    }

    public function show($id)
    {
        $document = PatientDocument::with('folder')->findOrFail($id);

        abort_unless($document->folder->isOwnedBy(auth()->user()), 403, "Not allowed");

        return PatientDocumentResource::make($document);
    }

    public function store(PatientDocumentRequest $request)
    {
        $attributes = $request->validated();

        $document = PatientDocument::create($attributes);

        return PatientDocumentResource::make($document)->additional([
            'message' => __("Document stored successfully")
        ]);
    }

    public function update(PatientDocumentRequest $request, $id)
    {
        $document = PatientDocument::with('folder')->findOrFail($id);

        abort_unless($document->folder->isOwnedBy(auth()->user()), 403, "Not allowed");

        $attributes = $request->validated();

        $document->update($attributes);

        return PatientDocumentResource::make($document)->additional([
            'message' => __("Document updated successfully")
        ]);
    }

    public function destroy($id)
    {
        $document = PatientDocument::with('folder')->findOrFail($id);

        abort_unless($document->folder->isOwnedBy(auth()->user()), 403, "Not allowed");

        $document->delete();

        return success(null, __("Document deleted successfully"));
    }
}
