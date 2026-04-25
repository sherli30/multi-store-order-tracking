@extends('layouts.app')

@section('title', 'Profil Admin')

@section('styles')
    <style>
        /* ── Layout ── */
        .profile-page-wrapper {
            display: grid !important;
            grid-template-columns: 280px 1fr !important;
            gap: 24px !important;
            align-items: start !important;
            width: 100% !important;
            box-sizing: border-box !important;
            /* Bust any flex parent trying to stack children */
            min-width: 0;
        }

        /* Ensure children don't stretch to 100% width */
        .profile-page-wrapper>* {
            min-width: 0;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 960px) {
            .profile-page-wrapper {
                grid-template-columns: 1fr !important;
            }

            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ── Side Card ── */
        .profile-side-card {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            text-align: center;
            padding-bottom: 28px;
            animation: rise 0.4s ease both;
        }

        .profile-hero {
            height: 110px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--blue) 60%, #7c3aed 100%);
            position: relative;
            overflow: hidden;
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(255, 255, 255, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 28px solid rgba(255, 255, 255, 0.07);
            bottom: -50px;
            right: -30px;
        }

        .profile-avatar-wrap {
            width: 96px;
            height: 96px;
            margin: -48px auto 16px;
            border-radius: 50%;
            background: var(--surface);
            border: 4px solid var(--panel);
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.18);
            overflow: hidden;
            cursor: pointer;
        }

        .profile-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, var(--accent), var(--blue));
        }

        .profile-upload-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            cursor: pointer;
            transition: opacity 0.2s;
            border-radius: 50%;
        }

        .profile-avatar-wrap:hover .profile-upload-overlay {
            opacity: 1;
        }

        .profile-name-text {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-1);
            padding: 0 16px;
        }

        .profile-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: var(--accent-dim);
            color: var(--accent);
            margin-top: 8px;
        }

        .profile-role-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            display: inline-block;
        }

        /* Quick stats strip */
        .profile-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px;
            background: var(--border);
            margin: 16px 0 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .profile-stat {
            background: var(--panel);
            padding: 14px 10px;
            text-align: center;
        }

        .profile-stat-value {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-1);
            line-height: 1;
        }

        .profile-stat-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 4px;
        }

        /* Info list */
        .profile-info-list {
            margin-top: 8px;
            padding: 0 12px;
            text-align: left;
        }

        .profile-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            transition: background 0.15s;
        }

        .profile-info-item:hover {
            background: var(--surface-2);
        }

        .profile-info-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--surface-2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-3);
            flex-shrink: 0;
        }

        .profile-info-content {
            min-width: 0;
        }

        .profile-info-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .profile-info-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Form Styles ── */
        .card {
            background: var(--panel);
            border-radius: 18px;
            box-shadow: var(--shadow-sm);
            animation: rise 0.4s ease both;
        }

        .card-head {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-1);
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-2);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            background: var(--surface);
            border: 1px solid var(--border-2);
            border-radius: 8px;
            font-family: var(--font);
            font-size: 13.5px;
            color: var(--text-1);
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: var(--panel);
        }

        .form-input::placeholder {
            color: var(--text-3);
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #4f51e8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        .error-text {
            color: var(--red);
            font-size: 11px;
            font-weight: 600;
            margin-top: 6px;
            display: block;
        }
    </style>
@endsection

@section('content')
    {{-- TIDAK ada #toastContainer di sini — sudah ada di layouts/app.blade.php --}}

    <div class="profile-page-wrapper"
        style="display:grid; grid-template-columns:280px 1fr; gap:24px; align-items:start; width:100%; box-sizing:border-box;">

        <!-- ── Left: Profile Summary Card ── -->
        <div style="position: sticky; top: 24px;">
            <div class="profile-side-card">
                <div class="profile-hero"></div>

                <div class="profile-avatar-wrap">
                    @if(auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" class="profile-avatar-img"
                            id="avatar-preview-img">
                    @else
                        <div class="profile-avatar-placeholder" id="avatar-placeholder-text">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <img src="" class="profile-avatar-img" id="avatar-preview-img" style="display: none;">
                    @endif
                    <label for="avatar_upload" class="profile-upload-overlay" title="Ubah Foto Profil">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <span style="font-size: 9px; font-weight: 800; margin-top: 4px; letter-spacing: 0.6px;">UBAH
                            FOTO</span>
                    </label>
                </div>

                <div class="profile-name-text">{{ auth()->user()->name }}</div>
                <div class="profile-role-badge">{{ ucfirst(auth()->user()->role) }}</div>

                <div class="profile-stats">
                    <div class="profile-stat">
                        <div class="profile-stat-value">{{ auth()->user()->created_at->diffInDays() }}</div>
                        <div class="profile-stat-label">Hari Aktif</div>
                    </div>
                    <div class="profile-stat">
                        <div class="profile-stat-value">{{ auth()->user()->created_at->format('Y') }}</div>
                        <div class="profile-stat-label">Bergabung</div>
                    </div>
                </div>

                <div class="profile-info-list">
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                </path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div class="profile-info-content">
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.18 12.82 19.79 19.79 0 0 1 1.11 4.14 2 2 0 0 1 3.09 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </div>
                        <div class="profile-info-content">
                            <div class="profile-info-label">Telepon</div>
                            <div class="profile-info-value">{{ auth()->user()->phone ?? 'Belum diisi' }}</div>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <div class="profile-info-content">
                            <div class="profile-info-label">Bergabung</div>
                            <div class="profile-info-value">{{ auth()->user()->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="profile-info-item">
                        <div class="profile-info-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="profile-info-content">
                            <div class="profile-info-label">Status Akun</div>
                            <div class="profile-info-value"
                                style="color: var(--green); display:flex; align-items:center; gap:5px;">
                                <span
                                    style="width:7px;height:7px;border-radius:50%;background:var(--green);display:inline-block;flex-shrink:0;"></span>
                                Aktif &amp; Terverifikasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- end sticky wrapper --}}

        <!-- ── Right: Forms ── -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
        </div>
    </div>

    @push('scripts')
        <script>
            function previewProfileImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.getElementById('avatar-preview-img');
                        const placeholder = document.getElementById('avatar-placeholder-text');
                        img.src = e.target.result;
                        img.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                }
            }
        </script>
    @endpush
@endsection
