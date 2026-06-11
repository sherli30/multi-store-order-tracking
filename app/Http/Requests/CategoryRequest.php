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
            'store_id.required' => 'Toko wajib dipilih terlebih dahulu.',
            'store_id.exists'   => 'Toko yang dipilih tidak terdaftar di sistem kami.',

            // Name
            'name.required'     => 'Nama kategori tidak boleh kosong.',
            'name.string'       => 'Nama kategori harus berupa teks.',
            'name.max'          => 'Nama kategori terlalu panjang, maksimal 100 karakter.',
            'name.unique'       => 'Kategori dengan nama ini sudah ada di toko tersebut. Silakan gunakan nama lain.',

            // Description
            'description.required' => 'Deskripsi kategori tidak boleh kosong.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max'    => 'Deskripsi terlalu panjang, maksimal 500 karakter.',

            // Is Active
            'is_active.accepted' => 'Status aktif kategori wajib diaktifkan saat pendaftaran baru.',
            'is_active.required' => 'Status aktif kategori wajib diisi.',
            'is_active.boolean'  => 'Status aktif harus berupa pilihan benar atau salah.',
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
