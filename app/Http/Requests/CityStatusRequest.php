<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'is_active.required' => 'Status kota wajib dipilih.',
            'is_active.boolean' => 'Status kota tidak valid. Harap pilih aktif atau nonaktif.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
