<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/',      // Wajib ada angka
                'regex:/[A-Za-z]/',   // Wajib ada huruf
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email tidak boleh kosong. Silakan masukkan alamat email Anda.',
            'email.email' => 'Format email tidak valid. Gunakan format yang benar (contoh: user@gmail.com).',
            'password.required' => 'Password wajib diisi untuk masuk.',
            'password.min' => 'Password terlalu pendek. Minimal harus 8 karakter.',
            'password.regex' => 'Password harus kombinasi huruf dan angka (contoh: User123).',
        ];
    }

    /**
     * Logika Autentikasi: Memisahkan error Email dan Password
     */
    public function authenticate()
    {
        $user = \App\Models\User::where('email', $this->email)->first();

        if (!$user) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Email tidak terdaftar dalam sistem kami. Silakan cek kembali atau daftar akun baru.',
            ], 404));
        }

        if (!\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Password yang Anda masukkan salah. Pastikan tidak ada salah ketik.',
            ], 401));
        }

        if (!$user->is_active) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Admin untuk pengaktifan.',
            ], 403));
        }

        return $user;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 422));
    }
}
