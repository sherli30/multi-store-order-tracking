<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('product_category')?->id;
        $isCreate = $this->isMethod('post');

        $rules = [
            'store_id' => [
                'required',
                'exists:stores,id'
            ],

            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                Rule::unique('product_categories', 'name')
                    ->where('store_id', $this->store_id)
                    ->ignore($categoryId),
            ],

            'description' => [
                'required',
                'string',
                'max:500'
            ],
        ];

        if ($isCreate) {
            $rules['is_active'] = ['accepted'];
        } else {
            $rules['is_active'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * Get custom validation messages with detailed breakdown.
     */
    public function messages(): array
    {
        return [
            // Store ID
            'store_id.required' => 'Toko wajib dipilih.',
            'store_id.exists'   => 'Toko tidak ditemukan.',

            // Name
            'name.required'     => 'Nama kategori wajib diisi.',
            'name.string'       => 'Nama kategori harus berupa teks.',
            'name.min'          => 'Nama kategori minimal 3 karakter.',
            'name.max'          => 'Nama kategori maksimal 100 karakter.',
            'name.unique'       => 'Nama kategori sudah digunakan.',

            // Description
            'description.required' => 'Deskripsi wajib diisi.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max'    => 'Deskripsi maksimal 500 karakter.',

            // Is Active
            'is_active.accepted' => 'Status wajib dipilih.',
            'is_active.required' => 'Status wajib dipilih.',
            'is_active.boolean'  => 'Status kategori tidak valid.',
        ];
    }

    /**
     * Normalize is_active from checkbox before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
