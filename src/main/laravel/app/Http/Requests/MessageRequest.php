<?php

namespace App\Http\Requests;

use App\Rules\UploadedFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'topic' => ['required', 'numeric', Rule::exists('message_topics', "id")],
            'body' => ['required', 'string', 'max:3000'],
            'attachments' => ['sometimes', 'nullable', 'array'],
            // TODO: validate these files
            'attachments.*' => ['string', new UploadedFile],
            'voice_message' => ['nullable', new UploadedFile],
        ];
    }
}
