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
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'is_active' => true,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil! Silakan login.',
                'data' => [
                    'user' => $user
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(\App\Http\Requests\Api\LoginRequest $request)
    {
        // Logika autentikasi (Cek Email & PW) sudah ditangani di LoginRequest
        $user = $request->authenticate();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil! Selamat datang kembali, ' . $user->name,
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ], 200);
    }

    public function updateProfile(\App\Http\Requests\Api\UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->only(['name', 'phone', 'address']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil Anda berhasil diperbarui dengan data terbaru.',
            'data' => [
                'user' => $user
            ]
        ], 200);
    }
}
