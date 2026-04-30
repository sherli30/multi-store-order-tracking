<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(\App\Http\Requests\Api\RegisterRequest $request)
    {
        // Data otomatis tervalidasi oleh RegisterRequest

        try {
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'phone'     => $request->phone,
                'password'  => Hash::make($request->password),
                'role'      => 'customer',
                'is_active' => true,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi berhasil! Akun Anda telah dibuat. Silakan login untuk melanjutkan.',
                'data'    => [
                    'user' => $user
                ]
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Registrasi gagal karena terjadi konflik data. Pastikan email dan nomor HP belum terdaftar.',
            ], 409);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server saat menyimpan data. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    public function login(\App\Http\Requests\Api\LoginRequest $request)
    {
        // Logika autentikasi (Cek Email & PW) sudah ditangani di LoginRequest
        $user = $request->authenticate();

        try {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'  => 'success',
                'message' => 'Login berhasil! Selamat datang kembali, ' . $user->name . '. Semoga hari Anda menyenangkan.',
                'data'    => [
                    'token' => $token,
                    'user'  => $user
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Login berhasil namun gagal membuat sesi. Silakan coba login kembali.',
            ], 500);
        }
    }

    public function updateProfile(\App\Http\Requests\Api\UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Sesi Anda telah berakhir. Silakan login kembali untuk melanjutkan.',
                ], 401);
            }

            // Deteksi perubahan SEBELUM update() agar perbandingan akurat
            $nameChanged   = $request->filled('name')    && $user->name    !== $request->name;
            $phoneChanged  = $request->filled('phone')   && $user->phone   !== $request->phone;
            $addressChanged = $request->filled('address') && $user->address !== $request->address;
            $avatarChanged = $request->hasFile('avatar');

            // ── Tidak ada perubahan sama sekali ──────────────────────────────
            if (!$nameChanged && !$phoneChanged && !$addressChanged && !$avatarChanged) {
                return response()->json([
                    'status'  => 'info',
                    'message' => 'Data Anda sudah sesuai. Tidak ada perubahan yang dilakukan.',
                    'data'    => [
                        'user' => $user
                    ]
                ], 200);
            }

            $data = $request->only(['name', 'phone', 'address']);

            // ── Proses unggah foto baru ───────────────────────────────────────
            if ($avatarChanged) {
                if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                    \Storage::disk('public')->delete($user->avatar);
                }

                $path = $request->file('avatar')->store('avatars', 'public');

                if (!$path) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Gagal mengunggah foto profil. Silakan coba lagi atau gunakan file lain.',
                    ], 500);
                }

                $data['avatar'] = $path;
            }

            $user->update($data);

            // ── Pesan sukses spesifik per field yang berubah ──────────────────
            $messages = [];

            if ($avatarChanged) {
                $messages[] = 'Foto profil berhasil diperbarui.';
            }
            if ($nameChanged) {
                $messages[] = 'Nama lengkap berhasil diperbarui.';
            }
            if ($phoneChanged) {
                $messages[] = 'Nomor telepon berhasil diperbarui.';
            }
            if ($addressChanged) {
                $messages[] = 'Alamat berhasil diperbarui.';
            }

            return response()->json([
                'status'  => 'success',
                'message' => [
                    'title' => 'Profil Berhasil Diperbarui',
                    'list'  => $messages,
                ],
                'data'    => [
                    'user' => $user->fresh()
                ]
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pembaruan profil gagal karena konflik data. Pastikan nomor HP belum digunakan akun lain.',
            ], 409);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server saat memperbarui profil. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        try {
            // Hapus token yang sedang aktif saja (bukan semua token)
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Anda telah berhasil keluar. Terima kasih telah menggunakan layanan kami.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat keluar dari sistem. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }
}
