<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignSurveyPatientRequest extends FormRequest
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
        return [
            'patient_id' => ['required'],
            'survey_id' => ['required', Rule::unique('patient_survey')->where(function ($query) {
                $query->where('patient_id', $this->patient_id)
                    ->where('survey_id', $this->survey_id);
            })]
        ];
    }

    public function messages()
    {
        return [
            'survey_id.unique'=>__('Survey is already assigned to patient.'),
            'survey_id.required'=>__('Please select survey!'),
        ];
    }
}
