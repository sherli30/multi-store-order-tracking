<div class="card"
    style="animation-delay: 0.1s; border-radius: 18px; overflow: hidden; border: 1px solid var(--border);">
    <!-- Card Header with gradient accent line -->
    <div class="card-head"
        style="border-bottom: 1px solid var(--border); padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
        <div class="card-title" style="display: flex; align-items: center; gap: 10px;">
            <span class="card-title-icon" style="
                width: 34px; height: 34px;
                border-radius: 9px;
                background: var(--accent-dim);
                display: flex; align-items: center; justify-content: center;
                color: var(--accent);
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </span>
            <div>
                <div style="font-size: 14px; font-weight: 800; color: var(--text-1);">Informasi Pribadi</div>
                <div style="font-size: 11px; color: var(--text-3); font-weight: 500; margin-top: 1px;">Data diri &amp;
                    kontak akun</div>
            </div>
        </div>
        <!-- Step indicator -->
        <div style="
            font-size: 10px;
            font-weight: 700;
            color: var(--text-3);
            background: var(--surface-2);
            border: 1px solid var(--border);
            padding: 4px 10px;
            border-radius: 100px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        ">Langkah 1 / 2</div>
    </div>

    <div style="padding: 28px 24px;">
        <!-- Info notice -->
        <div style="
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            background: var(--accent-dim);
            border: 1px solid rgba(99,102,241,0.18);
            border-radius: 10px;
            margin-bottom: 24px;
        ">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <p style="font-size: 12px; color: var(--accent); font-weight: 600; line-height: 1.5; margin: 0;">
                Perbarui data diri, nomor telepon, dan alamat email untuk mengakses seluruh fitur dashboard.
            </p>
        </div>

        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('patch')

            {{-- Hidden avatar input (triggered from sidebar) --}}
            <input type="file" id="avatar_upload" name="avatar" style="display: none;" accept="image/*"
                onchange="previewProfileImage(event)">

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">
                        Nama Lengkap
                        <span style="color: var(--red); margin-left: 2px;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span
                            style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); display:flex; pointer-events:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <input type="text" id="name" name="name" class="form-input" style="padding-left: 36px;"
                            value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">
                        Nomor Telepon
                        <span style="color: var(--red); margin-left: 2px;">*</span>
                    </label>
                    <div style="position: relative;">
                        <span
                            style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); display:flex; pointer-events:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.18 12.82 19.79 19.79 0 0 1 1.11 4.14 2 2 0 0 1 3.09 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg>
                        </span>
                        <input type="text" id="phone" name="phone" class="form-input" style="padding-left: 36px;"
                            value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    Alamat Email
                    <span style="color: var(--red); margin-left: 2px;">*</span>
                </label>
                <div style="position: relative;">
                    <span
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); display:flex; pointer-events:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" class="form-input" style="padding-left: 36px;"
                        value="{{ old('email', $user->email) }}" required placeholder="contoh@email.com">
                </div>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div style="
                                display: inline-flex;
                                align-items: center;
                                gap: 6px;
                                margin-top: 8px;
                                padding: 6px 10px;
                                background: #fef3c7;
                                border: 1px solid #f59e0b40;
                                border-radius: 6px;
                                font-size: 11px;
                                font-weight: 600;
                                color: #92400e;
                            ">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                            </path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        Email belum terverifikasi.
                        <a href="{{ route('verification.send') }}"
                            style="color: var(--accent); text-decoration: underline; font-weight: 700;"
                            onclick="event.preventDefault(); document.getElementById('send-verification').submit();">
                            Kirim ulang verifikasi
                        </a>
                    </div>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}"
                        style="display: none;">
                        @csrf
                    </form>
                @endif
            </div>

            <!-- Footer actions -->
            <div
                style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
