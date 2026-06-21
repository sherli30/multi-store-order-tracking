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
            'code' => [
                'required',
                'string',
                'regex:/^[A-Z0-9\-]+$/',
                'max:50',
                $provinceId ? 'unique:provinces,code,' . $provinceId : 'unique:provinces,code'
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
            'name.required' => 'Nama provinsi wajib diisi. Silakan masukkan nama provinsi yang valid.',
            'name.string' => 'Nama provinsi harus berupa teks biasa.',
            'name.max' => 'Nama provinsi terlalu panjang. Maksimal adalah 255 karakter.',
            'name.unique' => 'Nama provinsi sudah terdaftar di sistem. Silakan masukkan nama provinsi lain.',

            'code.required' => 'Kode provinsi wajib diisi.',
            'code.string' => 'Format kode provinsi tidak valid.',
            'code.regex' => 'Format kode provinsi tidak valid. Hanya gunakan huruf kapital, angka, dan strip (contoh: JBR, JTG).',
            'code.max' => 'Kode provinsi maksimal 50 karakter.',
            'code.unique' => 'Kode provinsi sudah digunakan. Silakan gunakan kode lain yang unik.',

            'is_active.accepted' => 'Status provinsi wajib diaktifkan saat penambahan baru.',
            'is_active.required' => 'Status provinsi wajib dipilih. Silakan tentukan apakah provinsi ini aktif atau tidak.',
            'is_active.boolean' => 'Status provinsi tidak valid. Harap pilih aktif atau nonaktif.',
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
            ->with('open_modal', 'provinceModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
