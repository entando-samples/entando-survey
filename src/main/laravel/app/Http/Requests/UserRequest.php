<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $isUpdate = request()->method() === 'PUT';
        $sometimes = Rule::when($isUpdate, 'sometimes');

        return [
            'name' => [$sometimes, 'required', 'string', 'max:255'],
            'email' => [$sometimes, 'required', 'email', Rule::unique('users', 'email')->whereNot('id', $this->route('user', -1))],
            'role' => [$sometimes, 'required', 'string', Rule::in([User::ADMIN, User::DOCTOR])],
            'password' => ['sometimes', 'nullable', 'min:8', 'confirmed'],
            'email_report' => ['nullable'],
        ];
    }
}
