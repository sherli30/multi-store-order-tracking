<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('password_success', 'Kata sandi berhasil diperbarui. Akun Anda sekarang lebih aman dengan kredensial baru.');
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Kesalahan Sistem',
                'list' => [
                    'Gagal memperbarui kata sandi.',
                    'Terjadi kesalahan pada sistem, silakan coba lagi.'
                ]
            ]);
        }
    }
}
