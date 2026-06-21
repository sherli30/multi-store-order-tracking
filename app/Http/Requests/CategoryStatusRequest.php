<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStatusRequest extends FormRequest
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
            'is_active.required' => 'Status wajib dipilih.',
            'is_active.boolean' => 'Status kategori tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $category = $this->route('product_category');
            
            if (!$category) {
                $validator->errors()->add('category', 'Kategori tidak ditemukan.');
                return;
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal mengubah status',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
