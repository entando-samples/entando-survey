<?php

namespace App\Http\Requests\Backend;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PathologyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isUpdate = request()->method() === 'PUT';
        return [
            'title' => [
                Rule::when($isUpdate, 'sometimes'),
                'required',
                'string',
                Rule::unique('pathologies', 'title')
                    ->ignore(optional($this->pathology)->id),
                'max:255'
            ]
        ];
    }
}
