<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        // Mengambil ID toko dari route untuk pengecekan unique saat update
        $storeId = $this->route('store')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Nama toko harus unik, mengabaikan ID toko ini sendiri saat update
                Rule::unique('stores', 'name')->ignore($storeId),
            ],

            'description' => [
                'required',
                'string',
                'max:5000'
            ],

            'is_active' => [
                'nullable',
                'boolean'
            ],

            'logo' => [
                // Logo wajib saat tambah, opsional saat update
                $this->isMethod('POST') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // Maksimal 2MB
            ],
        ];
    }

    /**
     * Get custom validation messages with detailed breakdown.
     */
    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'Nama toko tidak boleh kosong.',
            'name.string'   => 'Nama toko harus berupa teks.',
            'name.max'      => 'Nama toko terlalu panjang, maksimal 255 karakter.',
            'name.unique'   => 'Nama toko ini sudah terdaftar. Silakan gunakan nama lain.',

            // Description
            'description.required' => 'Deskripsi toko tidak boleh kosong.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'description.max'    => 'Deskripsi terlalu panjang, maksimal 5000 karakter.',

            // Is Active
            'is_active.boolean'  => 'Status aktif harus berupa nilai benar atau salah.',

            // Logo
            'logo.required' => 'Logo toko wajib diunggah.',
            'logo.image'    => 'File yang diunggah harus berupa gambar.',
            'logo.mimes'    => 'Format logo yang diizinkan hanya: JPG, JPEG, PNG, dan Webp.',
            'logo.max'      => 'Ukuran logo terlalu besar, maksimal adalah 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Membersihkan status checkbox agar konsisten sebagai boolean.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? true : false,
        ]);
    }
}
