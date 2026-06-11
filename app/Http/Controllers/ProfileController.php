<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui informasi profil (Nama, Email, HP, dan/atau Foto).
     * Pesan sukses disesuaikan dengan apa yang benar-benar berubah.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Deteksi perubahan SEBELUM fill() agar perbandingan akurat
        $nameChanged = $user->name !== $request->name;
        $emailChanged = $user->email !== $request->email;
        $phoneChanged = $user->phone !== $request->phone;
        $avatarChanged = $request->hasFile('avatar');

        // ── Tidak ada perubahan sama sekali ──────────────────────────────────
        if (!$avatarChanged && !$nameChanged && !$emailChanged && !$phoneChanged) {
            return Redirect::route('profile.edit')
                ->with('info', 'Data Anda sudah sesuai. Tidak ada perubahan yang dilakukan.');
        }

        // ── Proses unggah foto baru ───────────────────────────────────────────
        if ($avatarChanged) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');

            if (!$path) {
                return Redirect::route('profile.edit')
                    ->with('error', 'Gagal mengunggah foto profil. Silakan coba lagi atau gunakan file lain.');
            }

            $validated['avatar'] = $path;
        }
        // Terapkan semua perubahan sekaligus
        $user->fill($validated);
        $user->save();

        // ── MULTI NOTIFICATION (TOAST) ───────────────────────────────────────
        $messages = [];

        if ($avatarChanged) {
            $messages[] = 'Foto profil berhasil diperbarui.';
        }

        if ($nameChanged) {
            $messages[] = 'Nama lengkap berhasil diperbarui.';
        }

        if ($emailChanged) {
            $messages[] = 'Alamat email berhasil diperbarui.';
        }

        if ($phoneChanged) {
            $messages[] = 'Nomor telepon berhasil diperbarui.';
        }

        return Redirect::route('profile.edit')->with('success', [
            'title' => 'Profil Berhasil Diperbarui',
            'list' => $messages
        ]);
    }


}
