<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id|string'
        ];
    }

    // Failed validation method
    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }

    // create custom message for permissions.*
    public function messages(): array
    {
        return [
            'permissions.*.exists' => 'The selected permission :input is invalid.',
        ];
    }
}
