<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

function success($data = null, $message = null, $statusCode = 200)
{
    $message = $message ?: __('Success');

    if ($data instanceof LengthAwarePaginator) {
        $meta = [
            'from' => $data->firstItem(),
            'currentPage' => $data->currentPage(),
            'lastPage' => $data->lastPage(),
            'perPage' => $data->perPage(),
            'total' => $data->total()
        ];
        $data = $data->items();
    }

    return response()->json([
        'data' => $data,
        'meta' => $meta ?? null,
        'message' => $message,
    ], $statusCode);
}

function problem($message = null, $statusCode = 400, $code = null)
{
    $message = $message ?: __('Some problem occured');

    return response()->json([
        'message' => $message,
        'code' => $code,
    ], $statusCode);
}

function makeTestFile($type = 'image'): string
{
    $fileName = [
        'image' => 'test.png',
        'pdf' => 'test.pdf',
        'audio' => 'test.mp3',
    ][$type];

    $path = 'public/storage/' . config('rkare.uploads_folder');
    if (!file_exists($path)) {
        mkdir($path, 755);
    }

    $path = Storage::putFile(config('rkare.uploads_folder'), new \Illuminate\Http\File(public_path($fileName)), [
        'visibility' => "public"
    ]);

    return Storage::url($path);
}
