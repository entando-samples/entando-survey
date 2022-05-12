<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssingDocuemtPatientRequest extends FormRequest
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
            'document_id' => ['required', Rule::unique('document_patient')->where(function ($query) {
                $query->where('patient_id', $this->patient_id)
                    ->where('document_id', $this->document_id);
            })]
        ];
    }

    public function messages()
    {
        return [
            'document_id.unique'=>__('Document is already assigned to patient.'),
            'document_id.required'=>__('Please select document!'),
        ];
    }
}
