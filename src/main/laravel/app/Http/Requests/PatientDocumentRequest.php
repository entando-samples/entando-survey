<?php

namespace App\Http\Requests;

use App\Rules\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PatientDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = request()->method() === 'PUT';

        $sometimes = Rule::when($isUpdate, 'sometimes');

        $rules = [
            'folder_id' => ['required', 'integer', 'exists:patient_folders,id'],
            'title' => [$sometimes, 'required', 'string'],
            'note' => [$sometimes, 'nullable', 'string', 'max:2000'],
            'attachments' => [$sometimes, 'required', 'array'],
            'attachments.*' => ['required', new UploadedFile],
            'voice_message' => ['sometimes', 'nullable', new UploadedFile],
        ];

        if($isUpdate){
            unset($rules['folder_id']);
        }

        return $rules;
    }
}
