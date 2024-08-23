<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return ['otp' => 'required|string|filled|size:6',
            'password' => [
                'required',
                'confirmed',
                Password::min(6)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ]];
    }
    public function messages()
    {
        return [
            // return custom messages whole Request body cannot be empty
            'required' => 'The :attribute field is required.',
            // return if data is invalid
            'filled' => 'The :attribute field must not be empty.',
            // return if password is not the right format
            'password' => 'The :attribute must be at least 6 characters, contain at least one uppercase letter, one number, and one special character.',
            // return if OTP is not the right format
            'otp' => 'The :attribute must be a string.',
            'size' => 'The :attribute must be 6 characters.',
        ];
    }
}
