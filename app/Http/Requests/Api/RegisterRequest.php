<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email:rfc,dns|max:255|unique:users',
            'phone'    => 'required|string|max:20',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[0-9]/',
                'regex:/[A-Za-z]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama lengkap wajib diisi agar kami bisa mengenal Anda.',
            'email.required'    => 'Alamat email wajib diisi untuk keperluan login dan notifikasi.',
            'email.email'       => 'Format email tidak valid. Pastikan penulisan email sudah benar.',
            'email.unique'      => 'Maaf, email ini sudah terdaftar. Silakan gunakan email lain.',
            'phone.required'    => 'Nomor HP wajib diisi agar kurir dapat menghubungi Anda.',
            'password.required' => 'Password wajib diisi untuk keamanan akun.',
            'password.min'      => 'Password terlalu lemah. Gunakan minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok. Silakan ketik ulang.',
            'password.regex'    => 'Password harus berisi kombinasi huruf dan angka.',
        ];
    }

    /**
     * Override failedValidation agar tetap mengirim JSON (untuk Flutter)
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first(),
            'errors'  => $validator->errors()
        ], 422));
    }
}
