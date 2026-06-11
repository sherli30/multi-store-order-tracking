<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Toko Pakan</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --bg-gradient: linear-gradient(135deg, #f4f7fa 0%, #e9eef5 100%);
            --glass: rgba(255, 255, 255, 0.9);
            --panel: var(--glass);
            --text-1: #1e293b;
            --text-2: #475569;
            --text-3: #94a3b8;
            --border: #e2e8f0;
            --border-focus: #818cf8;
            --accent: var(--primary);
            --red: #ef4444;
            --green: #22c55e;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg-gradient);
            color: var(--text-1);
            font-family: var(--font);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        body::before,
        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            z-index: -1;
            filter: blur(100px);
            opacity: 0.4;
        }

        body::before {
            background: var(--primary-light);
            top: -150px;
            right: -100px;
        }

        body::after {
            background: #60a5fa;
            bottom: -150px;
            left: -100px;
        }

        .main-container {
            display: flex;
            width: 100%;
            max-width: 1100px;
            height: 85vh;
            background: var(--panel);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.7);
            z-index: 1;
        }

        /* ── Left panel ── */
        .left-panel {
            flex: 1.2;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            background-image: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2000');
            background-size: cover;
            background-position: center;
            color: #ffffff;
        }

        /* REVISI 1: Overlay gradien diperkuat agar teks lebih terbaca */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg,
                    rgba(0, 0, 0, 0.55) 0%,
                    rgba(79, 70, 229, 0.45) 50%,
                    rgba(0, 0, 0, 0.80) 100%);
            z-index: 1;
        }

        .left-top,
        .left-center,
        .left-bottom {
            position: relative;
            z-index: 2;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .logo-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #ffffff;
        }

        .logo-name span {
            color: #a5b4fc;
        }

        .left-tagline {
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.1;
            margin-bottom: 20px;
            max-width: 480px;
        }

        .left-sub {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
            line-height: 1.7;
            max-width: 400px;
        }

        .left-bottom {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 20px;
            border-radius: 100px;
            align-self: flex-start;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* REVISI 1b: Status dot dengan animasi pulse */
        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--green);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--green);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 6px var(--green);
                opacity: 1;
            }

            50% {
                box-shadow: 0 0 14px var(--green), 0 0 24px var(--green);
                opacity: 0.75;
            }
        }

        .status-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .version-tag {
            font-family: var(--mono);
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            margin-left: auto;
        }

        /* ── Right panel ── */
        .right-panel {
            width: 500px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            /* REVISI 3: Pastikan konten benar-benar vertikal center */
            justify-content: center;
            align-items: center;
            padding: 40px 60px;
            background: var(--panel);
        }

        .form-container {
            width: 100%;
        }

        .form-header {
            margin-bottom: 30px;
        }

        /* REVISI 5: Tambah margin-bottom pada eyebrow */
        .form-eyebrow {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 14px;
            /* diperbesar dari default */
        }

        /* REVISI 2: Judul diubah menjadi "Masuk Dashboard" */
        .form-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--text-1);
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 14px;
            color: var(--text-2);
            font-weight: 400;
            line-height: 1.5;
        }

        .field {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
            margin-bottom: 8px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-3);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .field-icon svg {
            width: 18px;
            height: 18px;
        }

        /* REVISI 4: Samakan warna background semua input — putih bersih */
        .field-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-1);
            font-family: var(--font);
            font-size: 14px;
            font-weight: 500;
            padding: 12px 16px 12px 48px;
            outline: none;
            transition: all 0.2s;
        }

        .field-input::placeholder {
            color: var(--text-3);
            font-weight: 400;
        }

        .field-input:focus {
            border-color: var(--border-focus);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .field-input:focus~.field-icon {
            color: var(--accent);
        }

        .toggle-pass {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-3);
            display: flex;
            align-items: center;
        }

        .toggle-pass:hover {
            color: var(--text-2);
        }

        .field-error {
            font-size: 12px;
            color: var(--red);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        /* REVISI 3b: Options row — pastikan sejajar horizontal */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-check {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .remember-check:checked {
            background: var(--accent);
            border-color: var(--accent);
        }

        .remember-check:checked::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 2px;
            width: 5px;
            height: 9px;
            border: 2px solid #fff;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .remember-text {
            font-size: 14px;
            color: var(--text-2);
            font-weight: 500;
            line-height: 1;
        }




        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, #4338ca 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: var(--font);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 20px -3px rgba(79, 70, 229, 0.5);
        }

        /* STATE LOADING */
        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.9;
            transform: scale(0.98);
        }

        /* WRAPPER */
        .btn-loading {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* SVG SPINNER */
        .spinner-icon {
            width: 18px;
            height: 18px;
            animation: rotate 0.8s linear infinite;
        }

        .spinner-icon circle {
            fill: none;
            stroke: #fff;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dasharray: 90;
            stroke-dashoffset: 60;
            animation: dash 1.2s ease-in-out infinite;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes dash {
            0% {
                stroke-dashoffset: 90;
            }

            50% {
                stroke-dashoffset: 20;
            }

            100% {
                stroke-dashoffset: 90;
            }
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .main-container {
                height: auto;
                max-width: 600px;
                flex-direction: column;
            }

            .left-panel {
                padding: 40px;
                min-height: 300px;
            }

            .right-panel {
                width: 100%;
                padding: 40px;
            }

            body {
                overflow: auto;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {

            .left-panel,
            .right-panel {
                padding: 30px 20px;
            }

            .form-title {
                font-size: 26px;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .logo-name {
                font-size: 18px;
            }
        }

        /* Tambahkan ini di dalam tag <style> */
        .field-input.is-invalid {
            border-color: var(--red) !important;
            background-color: #fffafb;
            /* Sedikit kemerahan agar kontras */
        }

        .field-input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        /* Styling Notifikasi Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            min-width: 300px;
            padding: 16px 20px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-left: 4px solid #ccc;
            animation: slideIn 0.3s ease forwards;
        }

        .toast.success {
            border-left-color: var(--green);
        }

        .toast.error {
            border-left-color: var(--red);
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-1);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-3);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .toast-close:hover {
            color: var(--text-1);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                transform: translateX(20px);
                opacity: 0;
            }
        }

        .toggle-pass svg {
            width: 18px;
            height: 18px;
        }

        .toggle-pass {
            z-index: 10;
        }
    </style>
</head>

<body>
    <div id="toastContainer" class="toast-container"></div>

    <div class="main-container">

        <div class="left-panel">
            <div class="left-top">
                <a href="{{ url('/') }}" class="logo">
                    <div class="logo-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo ayambebek"
                            style="width: 100%; height: auto;">
                    </div>
                    <span class="logo-name">Toko Pakan</span>
                </a>
            </div>

            <div class="left-center">
                <h1 class="left-tagline">
                    Kelola Pesanan &<br>
                    Pantau Pengiriman
                </h1>
                <p class="left-sub">
                    Sistem integrasi admin untuk efisiensi pemrosesan pesanan dan pelacakan <em>real-time</em>.
                </p>
            </div>

            <div class="left-bottom">
                <div class="status-dot"></div>
                <span class="status-text">Sistem Operasional Aktif</span>
            </div>
        </div>

        <div class="right-panel">
            <div class="form-container">

                <div class="form-header">
                    <div class="form-eyebrow">Portal Administrasi</div>
                    <div class="form-title">Masuk Dashboard 👋</div>
                    <div class="form-sub">Masuk untuk mengelola sistem dan pantau operasional.</div>
                </div>

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="field">
                        <label class="field-label" for="email">Alamat Email <span>*</span></label>
                        <div class="field-wrap">
                            <div class="field-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </div>
                            <input id="email" class="field-input @error('email') is-invalid @enderror" type="email"
                                name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus
                                autocomplete="username" />
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label" for="password">Kata Sandi <span>*</span></label>
                        <div class="field-wrap">
                            <div class="field-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                            <input id="password" class="field-input @error('password') is-invalid @enderror"
                                type="password" name="password" placeholder="••••••••" required
                                autocomplete="current-password" style="padding-right: 48px;" />
                            <button type="button" id="togglePass" class="toggle-pass">
                                <svg id="eyeShow" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>

                                <svg id="eyeHide" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    style="display:none;">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19C5 19 1 12 1 12a21.77 21.77 0 0 1 5.06-5.94"></path>
                                    <path d="M9.9 4.24A10.94 10.94 0 0 1 12 5c7 0 11 7 11 7a21.77 21.77 0 0 1-4.35 5.35"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="options-row">
                        <label class="remember-label">
                            <input id="remember_me" type="checkbox" class="remember-check" name="remember">
                            <span class="remember-text">Ingat saya</span>
                        </label>


                    </div>

                    <button type="submit" class="submit-btn" id="loginBtn">
                        <span class="btn-text">
                            Masuk Sekarang
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                style="width:18px;height:18px;">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </span>

                        <span class="btn-loading" style="display:none;">
                            <!-- ICON SPINNER -->
                            <svg class="spinner-icon" viewBox="0 0 50 50">
                                <circle cx="25" cy="25" r="20"></circle>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>

        const toastContainer = document.getElementById('toastContainer');

        // Fungsi untuk membuat Toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icon = type === 'success'
                ? '<svg width="20" height="20" fill="var(--green)" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                : '<svg width="20" height="20" fill="var(--red)" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';

            toast.innerHTML = `
            <div class="toast-content">
                ${icon}
                <span>${message}</span>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;

            toastContainer.appendChild(toast);

            // Hilang otomatis setelah 5 detik
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Ambil Error dari Laravel
        @if(session('status'))
            showToast("{{ session('status') }}", 'success');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                showToast("{{ $error }}", 'error');
            @endforeach
        @endif

        const form = document.querySelector('form');
        const btn = document.getElementById('loginBtn');

        const text = btn.querySelector('.btn-text');
        const loading = btn.querySelector('.btn-loading');

        form.addEventListener('submit', function () {
            btn.classList.add('loading');

            text.style.display = 'none';
            loading.style.display = 'flex';
        });
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('password');
        const eyeShow = document.getElementById('eyeShow');
        const eyeHide = document.getElementById('eyeHide');

        if (togglePass) {
            togglePass.addEventListener('click', () => {
                const isPass = passInput.type === 'password';

                passInput.type = isPass ? 'text' : 'password';
                eyeShow.style.display = isPass ? 'none' : 'block';
                eyeHide.style.display = isPass ? 'block' : 'none';
            });
        }
    </script>
</body>

</html>
