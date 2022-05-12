<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\PatientFolder;
use Illuminate\Http\Request;

class PatientFolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255']
        ]);

        $folder = PatientFolder::create([
            'title' => request('title'),
            'patient_id' => auth()->id(),
        ]);

        return success($folder->only('id', 'title'), __("Folder updated successfully"));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255']
        ]);

        $folder = PatientFolder::where('patient_id', auth()->id())->findOrFail($id);

        $folder->update(['title' => request('title')]);

        return success($folder->only('id', 'title'), __("Folder updated successfully"));
    }

    public function destroy($id)
    {
        $folder = PatientFolder::where('patient_id', auth()->id())->findOrFail($id);

        abort_if($folder->documents()->exists(), 400, "Documents exist for the folder");

        $folder->delete();

        return success(null, __("Folder deleted successfully"));
    }
}
