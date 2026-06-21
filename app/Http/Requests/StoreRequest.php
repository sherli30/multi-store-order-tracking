<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post');
        $store = $this->route('store');
        
        // Logo wajib jika:
        // 1. Sedang membuat toko baru (Create)
        // 2. Sedang mengedit tapi toko tersebut belum punya logo di database
        $logoRequired = $isCreate || ($store && !$store->logo);

        $rules = [
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'phone'             => ['required', 'numeric', 'digits_between:10,15'],
            'operational_hours' => ['required', 'string', 'max:255'],
            'logo'              => [$logoRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'address'           => ['required', 'string'],
            'province_id'       => ['required', 'exists:provinces,id'],
            'city_id'           => ['required', 'exists:cities,id'],
        ];

        if ($isCreate) {
            $rules['is_active'] = ['accepted'];
        } else {
            $rules['is_active'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama toko wajib diisi untuk identitas operasional.',
            'name.string' => 'Nama toko harus berupa karakter teks yang valid.',
            'name.max' => 'Nama toko tidak boleh lebih dari 255 karakter.',
            
            'logo.required' => 'File logo wajib dipilih.',
            'logo.image' => 'Format file harus JPG, JPEG, PNG atau WEBP.',
            'logo.mimes' => 'Format file harus JPG, JPEG, PNG atau WEBP.',
            'logo.max' => 'Ukuran file maksimal 2 MB.',
            
            'address.required' => 'Alamat lengkap toko wajib diisi untuk keperluan pengiriman.',
            'address.string' => 'Alamat lengkap harus berupa teks.',
            
            'province_id.required' => 'Provinsi asal toko wajib dipilih.',
            'province_id.exists' => 'Provinsi yang dipilih tidak terdaftar dalam sistem.',
            
            'city_id.required' => 'Kota atau kabupaten asal toko wajib dipilih.',
            'city_id.exists' => 'Kota atau kabupaten yang dipilih tidak terdaftar dalam sistem.',
            
            'phone.required' => 'Nomor telepon toko wajib diisi untuk koordinasi pengiriman.',
            'phone.numeric' => 'Nomor telepon harus berupa angka (0-9).',
            'phone.digits_between' => 'Nomor telepon harus berjumlah antara 10 sampai 15 digit.',
            
            'operational_hours.required' => 'Jam operasional toko wajib diisi sebagai informasi bagi customer.',
            'operational_hours.string' => 'Jam operasional harus berupa teks.',
            'operational_hours.max' => 'Jam operasional tidak boleh lebih dari 255 karakter.',
            
            'description.required' => 'Deskripsi toko wajib diisi untuk memberikan informasi detail mengenai toko.',
            'description.string' => 'Deskripsi harus berupa teks.',
            
            'is_active.accepted' => 'Status aktif toko wajib diaktifkan saat pendaftaran baru.',
            'is_active.required' => 'Status aktif toko wajib diisi.',
            'is_active.boolean' => 'Format status operasional tidak valid.',
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
