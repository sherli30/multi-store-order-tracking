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
            'email.required'    => 'Email tidak boleh kosong. Silakan masukkan alamat email Anda.',
            'email.email'       => 'Format email tidak valid. Gunakan format yang benar (contoh: user@gmail.com).',
            'password.required' => 'Password wajib diisi untuk masuk.',
            'password.min'      => 'Password terlalu pendek. Minimal harus 8 karakter.',
            // Pesan generik ini sengaja dikosongkan string-nya karena akan
            // digantikan oleh pesan spesifik di failedValidation().
            // Nilai dummy diperlukan agar key dikenali Laravel, tapi tidak
            // akan pernah tampil ke user karena selalu di-replace di bawah.
            'password.regex'    => '__replaced__',
        ];
    }

    /**
     * Logika Autentikasi: Memisahkan error Email dan Password.
     */
    public function authenticate()
    {
        $user = \App\Models\User::withTrashed()->where('email', $this->email)->first();

        if (!$user) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Email tidak terdaftar dalam sistem kami. Silakan cek kembali atau daftar akun baru.',
            ], 404));
        }

        if (!\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Password yang Anda masukkan salah. Pastikan tidak ada salah ketik.',
            ], 401));
        }

        if ($user->trashed()) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda telah dihapus. Silakan hubungi Admin untuk informasi lebih lanjut.',
            ], 403));
        }

        if (!$user->is_active) {
            throw new HttpResponseException(response()->json([
                'status'  => 'error',
                'message' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Admin untuk pengaktifan.',
            ], 403));
        }

        return $user;
    }

    /**
     * Override failedValidation agar tetap mengirim JSON (untuk Flutter).
     *
     * Masalah asal: (1) Laravel menghasilkan DUA pesan 'password.regex' karena
     * ada dua rule regex berbeda dengan satu key messages yang sama, sehingga
     * pesan muncul dobel. (2) Ada variabel $messages = [] yang dideklarasikan
     * tapi tidak pernah dipakai (sisa refactor lama) — sudah dihapus.
     * Fix regex: hapus semua pesan regex lama (termasuk '__replaced__'
     * placeholder), lalu tambahkan pesan spesifik sesuai karakter yang kurang.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        if ($errors->has('password')) {
            $raw = $this->input('password', '');

            // Hapus semua pesan regex (bisa muncul 1–2x tergantung berapa
            // rule regex yang gagal), termasuk placeholder '__replaced__'.
            $otherPasswordErrors = array_filter(
                $errors->get('password'),
                fn($msg) => $msg !== '__replaced__'
            );

            // Tambahkan pesan spesifik hanya jika karakter tersebut memang kurang.
            if (!preg_match('/[0-9]/', $raw)) {
                $otherPasswordErrors[] = 'Password harus mengandung minimal 1 angka (contoh: User123).';
            }
            if (!preg_match('/[A-Za-z]/', $raw)) {
                $otherPasswordErrors[] = 'Password harus mengandung minimal 1 huruf (contoh: User123).';
            }

            $errors->forget('password');
            foreach (array_values($otherPasswordErrors) as $msg) {
                $errors->add('password', $msg);
            }
        }

        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => $errors->first(),
            'errors'  => $errors,
        ], 422));
    }
}
