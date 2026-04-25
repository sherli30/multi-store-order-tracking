<style>
    /* Hilangkan icon eye bawaan browser */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none !important;
    }

    input::-webkit-credentials-auto-fill-button,
    input::-webkit-contacts-auto-fill-button {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
</style>

<div class="card"
    style="animation-delay: 0.2s; border-radius: 18px; overflow: hidden; border: 1px solid var(--border);">
    <!-- Card Header -->
    <div class="card-head"
        style="border-bottom: 1px solid var(--border); padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;">
        <div class="card-title" style="display: flex; align-items: center; gap: 10px;">
            <span style="
                width: 34px; height: 34px;
                border-radius: 9px;
                background: rgba(59,130,246,0.12);
                display: flex; align-items: center; justify-content: center;
                color: var(--blue);
                flex-shrink: 0;
            ">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </span>
            <div>
                <div style="font-size: 14px; font-weight: 800; color: var(--text-1);">Keamanan &amp; Kata Sandi</div>
                <div style="font-size: 11px; color: var(--text-3); font-weight: 500; margin-top: 1px;">Perbarui kata
                    sandi akun Anda</div>
            </div>
        </div>
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
        ">Langkah 2 / 2</div>
    </div>

    <div style="padding: 28px 24px;">
        <!-- Security tip -->
        <div style="
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            background: rgba(59,130,246,0.07);
            border: 1px solid rgba(59,130,246,0.15);
            border-radius: 10px;
            margin-bottom: 24px;
        ">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-top:1px;">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            <p style="font-size: 12px; color: var(--blue); font-weight: 600; line-height: 1.5; margin: 0;">
                Gunakan kata sandi yang panjang, unik, dan kombinasi huruf besar, angka, serta simbol. Kami tidak akan
                pernah meminta kata sandi Anda.
            </p>
        </div>

        <form method="post" action="{{ route('password.update') }}" novalidate>
            @csrf
            @method('put')

            <!-- Password strength hint -->
            <div style="
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 4px;
                margin-bottom: 20px;
                max-width: 400px;
            " id="strength-bars">
                <div style="height: 4px; border-radius: 4px; background: var(--border-2);"></div>
                <div style="height: 4px; border-radius: 4px; background: var(--border-2);"></div>
                <div style="height: 4px; border-radius: 4px; background: var(--border-2);"></div>
                <div style="height: 4px; border-radius: 4px; background: var(--border-2);"></div>
            </div>

            <div class="form-group" style="max-width: 400px;">
                <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                <div style="position: relative;">
                    <span
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); display:flex; pointer-events:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input type="password" id="current_password" name="current_password" class="form-input"
                        style="padding-left: 36px; padding-right: 40px; -webkit-appearance: none; appearance: none;"
                        autocomplete="current-password" placeholder="••••••••">
                    <button type="button" onclick="togglePassword('current_password', this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-3); display:flex; padding:0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group" style="max-width: 400px;">
                <label for="password" class="form-label">Kata Sandi Baru</label>
                <div style="position: relative;">
                    <span
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); display:flex; pointer-events:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4">
                            </path>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" class="form-input"
                        style="padding-left: 36px; padding-right: 40px; -webkit-appearance: none; appearance: none;"
                        autocomplete="new-password" placeholder="Min. 8 karakter"
                        oninput="checkPasswordStrength(this.value)">
                    <button type="button" onclick="togglePassword('password', this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-3); display:flex; padding:0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group" style="max-width: 400px;">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                <div style="position: relative;">
                    <span
                        style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-3); display:flex; pointer-events:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input"
                        style="padding-left: 36px; padding-right: 40px; -webkit-appearance: none; appearance: none;"
                        autocomplete="new-password" placeholder="Ulangi kata sandi baru">
                    <button type="button" onclick="togglePassword('password_confirmation', this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-3); display:flex; padding:0;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Footer actions -->
            <div
                style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="btn-primary"
                    style="background: var(--blue); box-shadow: 0 4px 12px var(--blue-dim);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    Ubah Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.style.color = isPassword ? 'var(--accent)' : 'var(--text-3)';
    }

    function checkPasswordStrength(val) {
        const bars = document.querySelectorAll('#strength-bars div');
        if (!bars.length) return;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const colors = ['var(--red)', '#f97316', '#eab308', 'var(--green)'];
        bars.forEach((b, i) => {
            b.style.background = i < score ? colors[score - 1] : 'var(--border-2)';
        });
    }
</script>
