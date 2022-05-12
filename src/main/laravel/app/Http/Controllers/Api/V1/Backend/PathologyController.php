<?php

namespace App\Http\Controllers\Api\V1\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PathologyRequest;
use App\Models\Pathology;
use Illuminate\Support\Facades\DB;

class PathologyController extends Controller
{
    public function index()
    {
        return success(
            Pathology::query()
                ->latest()
                ->when(request('search'), fn ($q) => $q->where('title', 'like', '%' . request('search') . '%'))
                ->withCount(['documents', 'surveys'])
                ->get()
        );
    }

    public function store(PathologyRequest $request)
    {
        $pathology = Pathology::query()
            ->create($request->validated());

        return success($pathology, "Pathology saved successfully..");
    }

    public function show(Pathology $pathology)
    {
        return success($pathology);
    }

    public function update(PathologyRequest $request, Pathology $pathology)
    {
        $pathology->update($request->validated());

        return success(null, 'Pathology updated successfully..');
    }

    public function destroy(Pathology $pathology)
    {
        DB::transaction(function () use ($pathology) {
            $pathology->documents()->sync([]);
            $pathology->surveys()->sync([]);

            $pathology->delete();
        });

        return success(null, 'Pathology deleted successfully');
    }

    public function realtionItems(Pathology $pathology)
    {
        return success([
            'documents' => $pathology->documents()->pluck('documents.title', 'documents.id'),
            'surveys' => $pathology->surveys()->pluck('surveys.title', 'surveys.id'),
        ]);
    }
}
