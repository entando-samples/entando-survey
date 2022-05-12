<?php

namespace App\Http\Requests\Backend;

use App\Rules\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isPut = request()->method() === "PUT";
        $sometimesIfUpdate = Rule::when($isPut, 'sometimes');

        return [
            'title' => [$sometimesIfUpdate, 'required', 'string', 'max:255'],
            'body' => ['sometimes', 'nullable', 'string'],
            'youtube_url' => ['sometimes', 'nullable', 'url'],
            'cover_image' => [$sometimesIfUpdate, 'required', (new UploadedFile)],
            'file' => ['sometimes', 'nullable', (new UploadedFile)->ofMimeTypes(['application/pdf'])],
            'images' => ['sometimes', 'nullable', 'array'],
            'images.*' => ['sometimes', 'nullable', new UploadedFile],
            'pathologies' => ['sometimes', 'nullable', 'array'],
            'pathologies.*' => [
                'exists:pathologies,id'
            ]
        ];
    }
}
