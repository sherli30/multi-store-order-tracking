<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourierRequest extends FormRequest
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
        $courier = $this->route('courier');
        $courierId = $courier ? $courier->id : null;

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                $courierId ? 'unique:couriers,code,' . $courierId : 'unique:couriers,code'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'contact_person' => [
                'required',
                'string',
                'max:255'
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:10',
                'max:20'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                $courierId ? 'unique:couriers,email,' . $courierId : 'unique:couriers,email'
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
     * Dibuat sangat detail untuk setiap kondisi input dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            // Nama Ekspedisi (name)
            'name.required' => 'Nama ekspedisi wajib diisi. Silakan masukkan nama penyedia layanan logistik (contoh: J&T Express, SiCepat).',
            'name.string' => 'Nama ekspedisi harus berupa teks biasa.',
            'name.max' => 'Nama ekspedisi terlalu panjang. Maksimal adalah 255 karakter.',

            // Kode Kurir (code)
            'code.required' => 'Kode kurir wajib diisi. Silakan masukkan kode identifikasi sistem (contoh: jnt, sicepat).',
            'code.string' => 'Kode kurir harus berupa teks biasa.',
            'code.max' => 'Kode kurir terlalu panjang. Maksimal adalah 50 karakter.',
            'code.unique' => 'Kode kurir sudah terdaftar di sistem. Silakan masukkan kode unik lain.',

            // Kontak
            'contact_person.required' => 'Nama kontak wajib diisi.',
            'contact_person.string' => 'Nama kontak harus berupa teks.',
            'contact_person.max' => 'Nama kontak maksimal 255 karakter.',

            // Nomor Telepon
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.string' => 'Nomor telepon harus berupa teks.',
            'phone_number.regex' => 'Format nomor telepon tidak valid.',
            'phone_number.min' => 'Nomor telepon minimal 10 digit.',
            'phone_number.max' => 'Nomor telepon maksimal 20 digit.',

            // Email
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh kurir lain.',

            // Status Aktif (is_active)
            'is_active.accepted' => 'Status aktif kurir wajib diaktifkan saat penambahan baru.',
            'is_active.required' => 'Status aktif kurir wajib diisi. Silakan tentukan apakah kurir ini aktif atau tidak.',
            'is_active.boolean' => 'Status aktif kurir harus berupa pilihan benar (aktif) atau salah (non-aktif).',
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
