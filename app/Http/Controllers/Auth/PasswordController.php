<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // Masalah asal: kode preg_match di bawah validateWithBag tidak pernah
        // dieksekusi karena validateWithBag sudah throw exception duluan saat
        // regex gagal. Fix: ganti 'password.regex' dengan '__replaced__' agar
        // Laravel tidak throw duluan, lalu tangkap di catch untuk diproses ulang.
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password'         => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'confirmed',
                    'regex:/[0-9]/',
                    'regex:/[A-Za-z]/',
                ],
            ], [
                'current_password.required'         => 'Kata sandi saat ini wajib diisi.',
                'current_password.current_password' => 'Kata sandi lama yang Anda masukkan salah. Pastikan tidak ada salah ketik.',
                'password.required'                 => 'Kata sandi baru wajib diisi.',
                'password.min'                      => 'Kata sandi baru terlalu pendek. Gunakan minimal 8 karakter agar akun tetap aman.',
                'password.max'                      => 'Kata sandi baru terlalu panjang. Maksimal 255 karakter.',
                'password.confirmed'                => 'Konfirmasi kata sandi baru tidak cocok. Pastikan keduanya sama.',
                // Nilai dummy — tidak pernah tampil ke user, selalu di-replace di catch bawah.
                'password.regex'                    => '__replaced__',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();

            // Pisahkan pesan regex: angka vs huruf
            if ($errors->has('password')) {
                $raw = $request->input('password', '');

                // Hapus semua pesan '__replaced__' (bisa muncul 1–2x)
                $otherPasswordErrors = array_filter(
                    $errors->get('password'),
                    fn($msg) => $msg !== '__replaced__'
                );

                // Tambahkan pesan spesifik hanya jika karakter tersebut memang kurang
                if (!preg_match('/[0-9]/', $raw)) {
                    $otherPasswordErrors[] = 'Kata sandi harus mengandung minimal 1 angka (contoh: Admin123).';
                }
                if (!preg_match('/[A-Za-z]/', $raw)) {
                    $otherPasswordErrors[] = 'Kata sandi harus mengandung minimal 1 huruf (contoh: Admin123).';
                }

                $errors->forget('password');
                foreach (array_values($otherPasswordErrors) as $msg) {
                    $errors->add('password', $msg);
                }
            }

            throw \Illuminate\Validation\ValidationException::withMessages(
                $errors->toArray()
            )->errorBag('updatePassword');
        }

        try {
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('password_success', 'Kata sandi berhasil diperbarui. Akun Anda sekarang lebih aman dengan kredensial baru.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui kata sandi. Terjadi kesalahan pada sistem, silakan coba lagi.');
        }
    }
}
