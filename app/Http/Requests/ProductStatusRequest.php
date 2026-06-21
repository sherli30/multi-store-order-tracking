<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('administrator');
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
            'is_active.required' => 'Data produk tidak valid.',
            'is_active.boolean' => 'Data produk tidak valid.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $product = $this->route('product');
            
            if (!$product) {
                $validator->errors()->add('product', 'Produk tidak ditemukan.');
                return;
            }

            $isActiveRequest = (bool) $this->is_active;

            if ($product->is_active && $isActiveRequest) {
                $validator->errors()->add('product', 'Produk sudah aktif.');
            }

            if (!$product->is_active && !$isActiveRequest) {
                $validator->errors()->add('product', 'Produk sudah nonaktif.');
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal mengubah status produk',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
