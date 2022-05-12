<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DocumentListResource;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        return DocumentListResource::collection(
            $user
                ->documents()
                ->get(['documents.id', 'documents.title', 'documents.slug'])
        );
    }

    public function show($id)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $document = $user->documents()->findOrFail($id);

        $user->markDocumentAsRead($document);

        return DocumentResource::make($document);
    }
}
