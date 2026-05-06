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

            // Menggunakan has() agar nilai kosong tetap terdeteksi sebagai "perubahan" 
            // sehingga validasi Required di Request API bisa terpancing.
            $nameChanged    = $request->has('name')    && $user->name    !== $request->name;
            $phoneChanged   = $request->has('phone')   && $user->phone   !== $request->phone;
            $addressChanged = $request->has('address') && $user->address !== $request->address;
            $provinceChanged = $request->has('province') && $user->province !== $request->province;
            $cityChanged    = $request->has('city')    && $user->city    !== $request->city;
            $postalChanged  = $request->has('postal_code') && $user->postal_code !== $request->postal_code;
            $avatarChanged  = $request->hasFile('avatar');

            // ── Tidak ada perubahan sama sekali ──────────────────────────────
            if (!$nameChanged && !$phoneChanged && !$addressChanged && !$provinceChanged && !$cityChanged && !$postalChanged && !$avatarChanged) {
                return response()->json([
                    'status'  => 'info',
                    'message' => 'Data Anda sudah sesuai. Tidak ada perubahan yang dilakukan.',
                    'data'    => [
                        'user' => $user
                    ]
                ], 200);
            }

            $data = $request->only(['name', 'phone', 'address', 'province', 'city', 'postal_code']);

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
            if ($provinceChanged) {
                $messages[] = 'Provinsi berhasil diperbarui.';
            }
            if ($cityChanged) {
                $messages[] = 'Kota berhasil diperbarui.';
            }
            if ($postalChanged) {
                $messages[] = 'Kode pos berhasil diperbarui.';
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

    /**
     * Simulation of Forgot Password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email tidak terdaftar di sistem kami.'
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            
            // Simulation: Reset password to 12345678
            $user->password = Hash::make('12345678');
            $user->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Password Anda telah berhasil direset menjadi "12345678" demi kemudahan demonstrasi. Silakan login kembali.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mereset password. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }
}
