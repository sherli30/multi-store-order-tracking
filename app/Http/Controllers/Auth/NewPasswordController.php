<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Masalah asal: kode preg_match di bawah validate tidak pernah
        // dieksekusi karena validate sudah throw exception duluan saat
        // regex gagal. Fix: ganti 'password.regex' dengan '__replaced__' agar
        // Laravel tidak throw duluan, lalu tangkap di catch untuk diproses ulang.
        try {
            $request->validate([
                'token'    => ['required'],
                'email'    => ['required', 'email'],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'confirmed',
                    'regex:/[0-9]/',
                    'regex:/[A-Za-z]/',
                ],
            ], [
                'token.required'     => 'Token reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan reset baru.',
                'email.required'     => 'Alamat email wajib diisi.',
                'email.email'        => 'Format email tidak valid. Gunakan format yang benar (contoh: user@gmail.com).',
                'password.required'  => 'Kata sandi baru wajib diisi.',
                'password.min'       => 'Kata sandi baru terlalu pendek. Gunakan minimal 8 karakter agar akun tetap aman.',
                'password.max'       => 'Kata sandi baru terlalu panjang. Maksimal 255 karakter.',
                'password.confirmed' => 'Konfirmasi kata sandi tidak cocok. Pastikan kedua kolom kata sandi sama.',
                // Nilai dummy — tidak pernah tampil ke user, selalu di-replace di catch bawah.
                'password.regex'     => '__replaced__',
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
            );
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        $successMessages = [
            Password::PASSWORD_RESET => 'Kata sandi Anda berhasil direset. Silakan login menggunakan kata sandi baru Anda.',
        ];

        $errorMessages = [
            Password::INVALID_USER    => 'Alamat email ini tidak terdaftar dalam sistem kami.',
            Password::INVALID_TOKEN   => 'Token reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan reset password baru.',
            Password::RESET_THROTTLED => 'Terlalu banyak percobaan reset password. Silakan tunggu beberapa menit sebelum mencoba lagi.',
        ];

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', $successMessages[$status] ?? __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => $errorMessages[$status] ?? __($status)]);
    }
}
