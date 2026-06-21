@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="error-page" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; text-align: center;">
    <div class="error-icon" style="margin-bottom: 24px;">
        <svg viewBox="0 0 24 24" width="80" height="80" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--red);">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
    </div>
    
    <h1 style="font-size: 32px; font-weight: 800; color: var(--text-1); margin-bottom: 12px;">403 | Akses Ditolak</h1>
    
    <p style="font-size: 16px; color: var(--text-2); max-width: 500px; line-height: 1.6; margin-bottom: 32px;">
        {{ $message ?? 'Halaman ini hanya untuk Administrator atau peran yang diizinkan. Anda tidak memiliki hak akses untuk melihat konten ini.' }}
    </p>

    <div class="action-buttons" style="display: flex; gap: 16px;">
        @if(auth()->check() && in_array(auth()->user()->role, ['administrator', 'logistics']))
            <a href="{{ route('dashboard') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--accent); color: white; border-radius: 10px; font-weight: 600; text-decoration: none; transition: background 0.2s;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Kembali ke Dashboard
            </a>
        @else
            <a href="/" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--accent); color: white; border-radius: 10px; font-weight: 600; text-decoration: none; transition: background 0.2s;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Kembali ke Beranda
            </a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: transparent; color: var(--text-2); border: 1.5px solid var(--border); border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Keluar
            </button>
        </form>
    </div>
</div>
@endsection
