<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ];
    }

    protected function prepareForValidation()
    {
        // make if permission null to empty array
        if ($this->permissions === null) {
            $this->merge([
                'permissions' => [],
            ]);
        }
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