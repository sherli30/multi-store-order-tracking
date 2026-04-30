<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi untuk mengirim tautan reset password.',
            'email.email'    => 'Format email tidak valid. Gunakan format yang benar (contoh: user@gmail.com).',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $successMessages = [
            Password::RESET_LINK_SENT => 'Tautan reset password telah dikirim ke alamat email Anda. Silakan periksa kotak masuk atau folder spam.',
        ];

        $errorMessages = [
            Password::INVALID_USER    => 'Alamat email ini tidak terdaftar dalam sistem kami. Periksa kembali atau daftar akun baru.',
            Password::RESET_THROTTLED => 'Permintaan reset password terlalu sering. Silakan tunggu beberapa menit sebelum mencoba lagi.',
        ];

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', $successMessages[$status] ?? __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => $errorMessages[$status] ?? __($status)]);
    }
}
