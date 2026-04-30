<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * ATURAN VALIDASI: Ketat untuk keamanan profesional
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/', // Wajib angka
                'regex:/[A-Za-z]/', // Wajib huruf
            ],
        ];
    }

    /**
     * PESAN VALIDASI: Bahasa Indonesia yang informatif dan mandiri
     */
    public function messages(): array
    {
        return [
            // Email
            'email.required' => 'Email tidak boleh kosong. Silakan ketik alamat email Admin Anda.',
            'email.email'    => 'Format email salah. Pastikan menggunakan tanda "@" (contoh: admin@gmail.com).',

            // Password
            'password.required' => 'Password wajib diisi untuk masuk ke sistem.',
            'password.min'      => 'Password terlalu pendek. Gunakan minimal 8 karakter agar akun tetap aman.',
            // Pesan generik ini sengaja dikosongkan string-nya karena akan
            // digantikan oleh pesan spesifik di failedValidation().
            // Nilai dummy diperlukan agar key dikenali Laravel, tapi tidak
            // akan pernah tampil ke user karena selalu di-replace di bawah.
            'password.regex'    => '__replaced__',
        ];
    }

    /**
     * LOGIKA AUTENTIKASI: Memberikan instruksi solusi saat gagal
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            // Cek apakah email ada di database
            $userExists = \App\Models\User::where('email', $this->email)->exists();

            if (!$userExists) {
                throw ValidationException::withMessages([
                    'email' => 'Email tidak terdaftar. Periksa kembali ejaan email atau gunakan akun Admin yang benar.',
                ]);
            }

            // Jika email ada tapi gagal login (Password Salah)
            throw ValidationException::withMessages([
                'password' => 'Password salah. Pastikan Caps Lock mati dan tidak ada salah ketik simbol atau angka.',
            ]);
        }

        // PROTEKSI ROLE: Tegas tanpa mengarahkan ke pihak lain
        $user = Auth::user();
        if ($user->role !== 'admin') {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Akses Ditolak! Portal ini khusus untuk Admin. Akun Anda (' . ($user->role ?? 'User') . ') tidak memiliki izin masuk.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * PROTEKSI KEAMANAN: Menjelaskan alasan pemblokiran
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Demi keamanan akun, akses diblokir sementara karena terlalu banyak mencoba. Silakan coba lagi dalam ' . $seconds . ' detik.',
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }

    /**
     * PISAH PESAN REGEX: Angka dan huruf ditampilkan sebagai error terpisah
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $errors = $validator->errors();

        if ($errors->has('password')) {
            $raw = $this->input('password', '');
            $passwordErrors = $errors->get('password');

            $filteredErrors = array_filter($passwordErrors, function ($msg) {
                return $msg !== '__replaced__';
            });

            if (!preg_match('/[0-9]/', $raw)) {
                $filteredErrors[] = 'Password harus mengandung minimal 1 angka (contoh: Admin123).';
            }
            if (!preg_match('/[A-Za-z]/', $raw)) {
                $filteredErrors[] = 'Password harus mengandung minimal 1 huruf (contoh: Admin123).';
            }

            $errors->forget('password');
            foreach (array_values($filteredErrors) as $msg) {
                $errors->add('password', $msg);
            }
        }

        throw (new \Illuminate\Validation\ValidationException($validator))
            ->redirectTo($this->getRedirectUrl());
    }
}
