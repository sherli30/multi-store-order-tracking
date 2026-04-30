<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('password.update')) {
            return $this->passwordRules();
        }

        return $this->profileRules();
    }

    /**
     * Override failedValidation agar errorBag bisa kondisional.
     * Ini satu-satunya cara yang benar dalam satu FormRequest.
     * $errorBag tidak bisa di-set via method lain karena Laravel
     * membacanya langsung dari sini saat exception dilempar.
     */
    protected function failedValidation(Validator $validator): void
    {
        $bag = $this->routeIs('password.update') ? 'updatePassword' : 'default';

        // Pisahkan pesan regex password: angka vs huruf
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

        throw (new ValidationException($validator))
            ->errorBag($bag)
            ->redirectTo($this->getRedirectUrl());
    }

    // ── Rules profil ─────────────────────────────────────────────────────────

    private function profileRules(): array
    {
        return [
            'name'  => ['required', 'string', 'min:3', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,dns',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'  => ['required', 'numeric', 'digits_between:10,14'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    // ── Rules password ───────────────────────────────────────────────────────

    private function passwordRules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
                'regex:/[0-9]/',
                'regex:/[A-Za-z]/',
            ],
        ];
    }

    // ── Pesan validasi ───────────────────────────────────────────────────────

    public function messages(): array
    {
        return [
            // Nama
            'name.required' => 'Nama lengkap tidak boleh kosong. Silakan masukkan nama asli Anda.',
            'name.string'   => 'Nama harus berupa teks, bukan angka atau karakter khusus.',
            'name.min'      => 'Nama terlalu pendek. Gunakan minimal 3 karakter agar data valid.',
            'name.max'      => 'Nama terlalu panjang. Maksimal 100 karakter.',

            // Email
            'email.required'   => 'Email wajib diisi agar akun tetap dapat diverifikasi.',
            'email.email'      => 'Format email salah. Pastikan menggunakan tanda "@" (contoh: admin@gmail.com).',
            'email.lowercase'  => 'Email harus ditulis dalam huruf kecil semua (contoh: admin@gmail.com).',
            'email.unique'     => 'Email ini sudah terdaftar di sistem. Silakan gunakan alamat email lain.',

            // Nomor HP
            'phone.required'       => 'Nomor Telepon tidak boleh kosong. Silakan masukkan nomor telepon Anda.',
            'phone.numeric'        => 'Nomor HP harus berupa angka.',
            'phone.digits_between' => 'Nomor HP tidak valid. Pastikan terdiri dari 10 sampai 14 digit angka.',
            'phone.max'            => 'Nomor HP terlalu panjang. Maksimal 14 digit angka.',

            // Avatar / Foto
            'avatar.image' => 'File yang Anda pilih bukan gambar. Gunakan format JPG, PNG, atau WebP.',
            'avatar.mimes' => 'Format gambar tidak didukung. Gunakan file .jpg, .png, atau .webp.',
            'avatar.max'   => 'Ukuran foto terlalu besar. Maksimal ukuran file adalah 2 MB.',

            // Password saat ini
            'current_password.required'         => 'Mohon masukkan kata sandi Anda saat ini.',
            'current_password.current_password' => 'Kata sandi lama yang Anda masukkan salah. Pastikan tidak ada salah ketik.',

            // Password baru
            'password.required'  => 'Silakan tentukan kata sandi baru Anda untuk meningkatkan keamanan.',
            'password.min'       => 'Password terlalu pendek. Gunakan minimal 8 karakter agar akun tetap aman.',
            'password.max'       => 'Password terlalu panjang. Maksimal adalah 255 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok. Pastikan keduanya sama.',
            // Pesan generik ini sengaja dikosongkan string-nya karena akan
            // digantikan oleh pesan spesifik di failedValidation().
            // Nilai dummy diperlukan agar key dikenali Laravel, tapi tidak
            // akan pernah tampil ke user karena selalu di-replace di bawah.
            'password.regex'     => '__replaced__',
        ];
    }
}
