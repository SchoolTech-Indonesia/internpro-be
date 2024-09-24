<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use function Laravel\Prompts\error;

class PartnerRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $uuid = $this->route('partner');
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('partners')->ignore($uuid, 'uuid'),
            ],
            'address' => 'required|string',
            // 'file_sk' => 'required|mimes:jpeg,jpg,png',
            'logo' => 'required|mimes:jpeg,jpg,png',
            // 'file_sk' => 'required|mimes:pdf',
            'file_sk' => 'required|mimes:pdf',
            'number_sk' => [
                'required',
                'string',
                'digits_between:1,255',
                'numeric',
                Rule::unique('partners')->ignore($uuid, 'uuid'),
            ],
            'end_date_sk' => 'required|string|date',
        ];
    }

    protected function passedValidation()
    {
        // TODO : Image Conversion
//        $logo = ImageManager::gd()->read($this->file('logo')->getRealPath());
//        $encoded = $logo->toJpeg(75)->toFilePointer();
//        $this->merge(['logo' => $encoded]);
    }

    // Failed validation method
    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $this->validator = $validator;
    }
}
