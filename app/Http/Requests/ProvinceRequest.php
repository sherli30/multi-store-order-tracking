<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProvinceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $province = $this->route('province');
        $provinceId = $province ? $province->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $provinceId ? 'unique:provinces,name,' . $provinceId : 'unique:provinces,name'
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama provinsi wajib diisi. Silakan masukkan nama provinsi yang valid.',
            'name.string' => 'Nama provinsi harus berupa teks biasa.',
            'name.max' => 'Nama provinsi terlalu panjang. Maksimal adalah 255 karakter.',
            'name.unique' => 'Nama provinsi sudah terdaftar di sistem. Silakan masukkan nama provinsi lain.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag)
            ->with('open_modal', 'provinceModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
