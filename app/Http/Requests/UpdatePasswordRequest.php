<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    protected $errorBag = 'updatePassword';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('Kata sandi harus mengandung minimal 1 angka (contoh: Toko1234).');
                    }
                    if (!preg_match('/[A-Za-z]/', $value)) {
                        $fail('Kata sandi harus mengandung minimal 1 huruf (contoh: Toko1234).');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi lama yang Anda masukkan salah. Pastikan tidak ada salah ketik.',
            'password.required'                 => 'Kata sandi baru wajib diisi.',
            'password.min'                      => 'Kata sandi baru terlalu pendek. Gunakan minimal 8 karakter agar akun tetap aman.',
            'password.max'                      => 'Kata sandi baru terlalu panjang. Maksimal 255 karakter.',
            'password.confirmed'                => 'Konfirmasi kata sandi baru tidak cocok. Pastikan keduanya sama.',
        ];
    }
}
