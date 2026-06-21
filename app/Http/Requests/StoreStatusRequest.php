<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'is_active' => ['required', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'is_active.required' => 'Data toko tidak valid.',
            'is_active.boolean' => 'Data toko tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $store = $this->route('store');
            
            if (!$store) {
                $validator->errors()->add('store', 'Toko tidak ditemukan.');
                return;
            }

            $isActiveRequest = (bool) $this->is_active;

            if ($store->is_active && $isActiveRequest) {
                $validator->errors()->add('store', 'Toko sudah aktif.');
            }

            if (!$store->is_active && !$isActiveRequest) {
                $validator->errors()->add('store', 'Toko sudah nonaktif.');
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal mengubah status toko',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
