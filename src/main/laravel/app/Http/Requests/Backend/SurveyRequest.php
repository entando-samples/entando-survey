<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $isPut = request()->method() === "PUT";
        $sometimesIfUpdate = Rule::when($isPut, 'sometimes');

        return [
            'title' => [$sometimesIfUpdate, 'required', 'string', 'max:255'],
            'description' => [$sometimesIfUpdate, 'nullable', 'string'],
            'pathologies' => ['sometimes', 'array'],
            'pathologies.*' => [
                'exists:pathologies,id'
            ],
            'questions' => [$sometimesIfUpdate, 'required', 'array'],
            'questions.*' => [
                'exists:questions,id'
            ],
            'warning_answers' => ['sometimes', 'array'],
            'warning_answers.*' => ['exists:answers,id'],
        ];
    }
}
