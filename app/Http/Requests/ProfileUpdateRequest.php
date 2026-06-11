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
        return $this->profileRules();
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
        ];
    }
}
