<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function store()
    {
        $payload = request()->file();

        if (!$payload) {
            return problem();
        }

        $file = reset($payload);

        $path = $file->storePublicly(config('rkare.uploads_folder'), config('filesystems.default'));

        return Storage::url($path);
    }

    public function destroy()
    {
        $path = request()->getContent();

        if (empty($path)) {
            return problem();
        }

        $file = pathinfo($path);

        $path =  config('rkare.uploads_folder') . "/{$file['basename']}";

        Storage::delete($path);

        return success(null, null, 204);
    }

    public function stream()
    {
        abort_unless(request('path'), 400, "Please provide file path");

        $file = pathinfo(request('path'));

        try {
            $path =  config('rkare.uploads_folder') . "/{$file['basename']}";

            return Storage::response($path);
        } catch (\League\Flysystem\FileNotFoundException $e) {
            abort(404, "File not found");
        }
    }
}
