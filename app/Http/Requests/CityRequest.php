<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
        return [
            'province_id' => [
                'required',
                'exists:provinces,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'type' => [
                'required',
                'string',
                'max:50'
            ],
            'postal_code' => [
                'required',
                'string',
                'max:10'
            ],
            'code' => [
                'required',
                'string',
                'regex:/^[A-Z0-9\-]+$/',
                'max:50',
                $this->route('city') ? 'unique:cities,code,' . $this->route('city')->id : 'unique:cities,code'
            ],
        ];

        if ($this->isMethod('POST')) {
            $rules['is_active'] = ['accepted'];
        } else {
            $rules['is_active'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'province_id.required' => 'Provinsi wajib dipilih. Silakan pilih salah satu provinsi yang tersedia.',
            'province_id.exists' => 'Provinsi yang Anda pilih tidak terdaftar di sistem.',
            'name.required' => 'Nama kota wajib diisi. Silakan masukkan nama kota atau kabupaten.',
            'name.string' => 'Nama kota harus berupa teks biasa.',
            'name.max' => 'Nama kota terlalu panjang. Maksimal adalah 255 karakter.',
            'type.required' => 'Tipe wilayah wajib dipilih. Silakan pilih Kota atau Kabupaten.',
            'type.string' => 'Tipe wilayah harus berupa teks biasa.',
            'type.max' => 'Tipe wilayah tidak boleh lebih dari 50 karakter.',
            'postal_code.required' => 'Kode pos wajib diisi. Silakan masukkan kode pos yang valid.',
            'postal_code.string' => 'Kode pos harus berupa teks atau angka.',
            'postal_code.max' => 'Kode pos terlalu panjang. Maksimal adalah 10 karakter.',
            'code.required' => 'Kode kota wajib diisi.',
            'code.string' => 'Format kode kota tidak valid.',
            'code.regex' => 'Format kode kota tidak valid. Hanya gunakan huruf kapital, angka, dan strip (contoh: BDG, JKT).',
            'code.max' => 'Kode kota maksimal 50 karakter.',
            'code.unique' => 'Kode kota sudah digunakan. Silakan gunakan kode lain yang unik.',
            'is_active.accepted' => 'Status kota wajib diaktifkan saat penambahan baru.',
            'is_active.required' => 'Status kota wajib dipilih. Silakan tentukan apakah kota ini aktif atau tidak.',
            'is_active.boolean' => 'Status kota tidak valid. Harap pilih aktif atau nonaktif.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag)
            ->with('open_modal', 'cityModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
