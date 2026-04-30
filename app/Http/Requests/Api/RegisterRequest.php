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
            'phone'    => 'required|string|max:20|unique:users',
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
            'name.required'      => 'Nama lengkap wajib diisi agar kami bisa mengenal Anda.',
            'email.required'     => 'Alamat email wajib diisi untuk keperluan login dan notifikasi.',
            'email.email'        => 'Format email tidak valid. Pastikan penulisan email sudah benar.',
            'email.unique'       => 'Maaf, email ini sudah terdaftar. Silakan gunakan email lain.',
            'phone.required'     => 'Nomor HP wajib diisi agar kurir dapat menghubungi Anda.',
            'phone.unique'       => 'Maaf, nomor HP ini sudah terdaftar. Silakan gunakan nomor lain.',
            'password.required'  => 'Password wajib diisi untuk keamanan akun.',
            'password.min'       => 'Password terlalu lemah. Gunakan minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Silakan ketik ulang.',
            // Pesan generik ini sengaja dikosongkan string-nya karena akan
            // digantikan oleh pesan spesifik di failedValidation().
            // Nilai dummy diperlukan agar key dikenali Laravel, tapi tidak
            // akan pernah tampil ke user karena selalu di-replace di bawah.
            'password.regex'     => '__replaced__',
        ];
    }

    /**
     * Override failedValidation agar tetap mengirim JSON (untuk Flutter).
     *
     * Masalah asal: Laravel menghasilkan DUA pesan 'password.regex' karena
     * ada dua rule regex berbeda dengan satu key messages yang sama, sehingga
     * pesan muncul dobel. Fix: hapus semua pesan regex lama (termasuk
     * '__replaced__' placeholder), lalu tambahkan pesan spesifik sesuai
     * karakter apa yang benar-benar kurang.
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
