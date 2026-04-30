<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login khusus Admin.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Menangani proses masuk (login) Admin.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Jalankan validasi, cek password, dan cek role 'admin'
        // Semua logika ini sudah ada di dalam LoginRequest yang kita buat tadi.
        $request->authenticate();

        try {
            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();

            // Ambil data admin yang login untuk pesan selamat datang
            $user = Auth::user();

            // Redirect langsung ke dashboard admin dengan pesan sukses yang profesional
            return redirect()->route('dashboard')
                ->with('success', 'Akses Berhasil. Selamat datang kembali, ' . $user->name . '. Panel administrasi telah siap.');
        } catch (\Exception $e) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat memproses sesi login. Silakan coba masuk kembali.');
        }
    }

    /**
     * Menangani proses keluar (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect kembali ke login dengan pesan yang ramah
        return redirect('/')->with('success', 'Anda telah berhasil keluar dari sistem. Terima kasih atas dedikasi Anda.');
    }
}
