@extends('layouts.app')

@section('title', 'Edit Toko — ' . $store->name)
@section('styles')
    /* ── PAGE HEADER ─── */
    .page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 16px;
    }

    .page-header-left h1 {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--text-1);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 14px;
    }

    .page-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--accent) 0%, #7c3aed 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 24px color-mix(in srgb, var(--accent) 25%, transparent);
    transform: perspective(1000px) rotateY(-5deg);
    }

    .page-icon svg {
    width: 24px;
    height: 24px;
    color: #fff;
    }

    .page-header-left p {
    font-size: 13px;
    color: var(--text-3);
    margin-left: 54px;
    font-weight: 500;
    line-height: 1.5;
    }

    /* ── BUTTONS ─── */
    .btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 22px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-family: var(--font);
    font-size: 13px;
    font-weight: 600;
    color: var(--text-2);
    background: var(--panel);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-back:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: color-mix(in srgb, var(--accent) 5%, transparent);
    transform: translateX(-3px);
    }

    .btn-back svg {
    width: 16px;
    height: 16px;
    }

    /* ── Delete Modal ────────────────────────────── */
    .modal-overlay {
        position: fixed; inset: 0;
        background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px);
        z-index: 2000; display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: all 0.2s;
    }
    .modal-overlay.open { opacity: 1; visibility: visible; }
    .modal-box {
        background: var(--panel); border-radius: 16px; padding: 28px;
        width: 420px; max-width: 90vw; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transform: scale(0.95) translateY(10px); transition: transform 0.2s;
    }
    .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
    .modal-icon {
        width: 52px; height: 52px; background: var(--red-dim); border-radius: 14px;
        display: flex; align-items: center; justify-content: center; margin-bottom: 16px;
    }
    .modal-icon svg { width: 24px; height: 24px; color: var(--red); }
    .modal-title { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 6px; }
    .modal-desc { font-size: 13px; color: var(--text-2); margin-bottom: 22px; line-height: 1.6; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .btn-cancel {
        padding: 9px 18px; border: 1px solid var(--border); border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--surface); color: var(--text-2); cursor: pointer; transition: all 0.15s;
    }
    .btn-cancel:hover { border-color: var(--border-2); color: var(--text-1); }
    .btn-danger {
        padding: 9px 18px; border: none; border-radius: 8px;
        font-family: var(--font); font-size: 13px; font-weight: 600;
        background: var(--red); color: #fff; cursor: pointer; transition: all 0.15s;
        box-shadow: 0 2px 8px rgba(220,38,38,0.25);
    }
    .btn-danger:hover { background: #b91c1c; transform: translateY(-1px); }

    .btn-outline-danger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border: 1.5px solid rgba(220, 38, 38, 0.3);
        border-radius: 10px;
        font-family: var(--font);
        font-size: 14px;
        font-weight: 700;
        color: var(--red);
        background: transparent;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-outline-danger:hover {
        background: var(--red-dim);
        border-color: rgba(220, 38, 38, 0.5);
    }

    /* ── FORM LAYOUT ─── */
    .form-container {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 28px;
    align-items: start;
    }

    @media(max-width: 1024px) {
    .form-container {
    grid-template-columns: 1fr;
    }
    }

    .form-main {
    display: flex;
    flex-direction: column;
    gap: 28px;
    }

    .form-sidebar {
    display: flex;
    flex-direction: column;
    gap: 28px;
    position: sticky;
    top: 32px;
    }

    /* ── FORM CARDS ─── */
    .form-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 1px 4px color-mix(in srgb, var(--text-1) 4%, transparent);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    }

    .form-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--accent), transparent);
    opacity: 0;
    transition: opacity 0.3s;
    }

    .form-card:hover {
    box-shadow: 0 8px 20px color-mix(in srgb, var(--text-1) 8%, transparent);
    }

    .form-card:hover::before {
    opacity: 1;
    }

    .form-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, var(--surface) 0%, color-mix(in srgb, var(--accent) 2%, var(--surface)) 100%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    }

    .form-card-header svg {
    width: 20px;
    height: 20px;
    color: var(--accent);
    flex-shrink: 0;
    }

    .form-card-header-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-1);
    letter-spacing: -0.01em;
    }

    .form-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    }

    /* ── FORM FIELDS ─── */
    .field-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    }

    .field-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-2);
    display: flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    }

    .field-label span {
    color: var(--red);
    font-size: 13px;
    }

    .field-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 9px;
    font-family: var(--font);
    font-size: 13px;
    color: var(--text-1);
    background: var(--surface);
    outline: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-sizing: border-box;
    font-weight: 500;
    }

    .field-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-glow);
    background: var(--panel);
    transform: translateY(-1px);
    }

    .field-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: var(--surface-2);
    }

    .field-input.is-invalid {
    border-color: var(--red);
    }

    .field-textarea {
    min-height: 130px;
    resize: vertical;
    line-height: 1.6;
    font-weight: 400;
    }

    .field-hint {
    font-size: 12px;
    color: var(--text-3);
    line-height: 1.5;
    font-weight: 500;
    }

    .field-error {
    font-size: 12px;
    color: var(--red);
    font-weight: 600;
    margin-top: 4px;
    }

    /* ── TOGGLE CARD ─── */
    .toggle-card {
    padding: 14px 18px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--surface) 0%, color-mix(in srgb, var(--accent) 1%, var(--surface)) 100%);
    border: 1.5px solid var(--border);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toggle-card:hover {
    border-color: var(--accent);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--accent) 10%, transparent);
    }

    .toggle-switch {
    position: relative;
    width: 48px;
    height: 28px;
    flex-shrink: 0;
    }

    .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    }

    .toggle-slider {
    position: absolute;
    inset: 0;
    background: #cbd5e1;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.08);
    }

    .toggle-slider::before {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    left: 3px;
    top: 3px;
    background: #fff;
    border-radius: 50%;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    .toggle-switch input:checked + .toggle-slider {
    background: var(--green);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--green) 35%, transparent);
    }

    .toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(20px);
    }

    .toggle-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-1);
    }

    /* ── IMAGE UPLOAD & GALLERY ─── */
    .existing-images-grid { display: flex; gap: 12px; flex-wrap: wrap; }

    .image-thumb {
    position: relative;
    width: 140px;
    height: 140px;
    border-radius: 12px;
    overflow: hidden;
    border: 1.5px solid var(--border);
    box-shadow: 0 4px 12px color-mix(in srgb, var(--text-1) 8%, transparent);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .image-thumb:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px color-mix(in srgb, var(--text-1) 12%, transparent);
    }

    .image-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    }

    /* ── IMAGE ACTIONS ── */
    .image-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    opacity: 0;
    transition: opacity 0.2s;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    padding: 16px 4px 4px 4px;
    gap: 4px;
    justify-content: space-between;
    }
    .image-thumb:hover .image-actions {
    opacity: 1;
    }
    .img-action-btn {
    flex: 1;
    font-size: 10px;
    font-weight: 600;
    padding: 4px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    color: white;
    }
    .btn-replace { background: var(--accent); }
    .btn-replace:hover { background: color-mix(in srgb, var(--accent) 80%, black); }
    .btn-delete { background: var(--red); }
    .btn-delete:hover { background: color-mix(in srgb, var(--red) 80%, black); }

    .image-upload-zone {
    border: 2.5px dashed var(--border);
    border-radius: 13px;
    padding: 36px;
    text-align: center;
    background: linear-gradient(135deg, var(--surface) 0%, color-mix(in srgb, var(--accent) 1%, var(--surface)) 100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    }

    .image-upload-zone:hover {
    border-color: var(--accent);
    background: color-mix(in srgb, var(--accent) 4%, var(--surface));
    box-shadow: 0 8px 20px color-mix(in srgb, var(--accent) 15%, transparent);
    transform: translateY(-2px);
    }

    /* ── IMAGE MODAL ─── */
    .image-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
    }
    .image-modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.5);
    }
    .image-modal-close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: #f1f1f1;
    font-size: 35px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
    }

    /* ── AUDIT TRAIL ─── */
    .audit-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1px dashed var(--border);
    }

    .audit-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
    }

    .audit-label {
    font-size: 12px;
    color: var(--text-3);
    font-weight: 600;
    }

    .audit-value {
    font-size: 12px;
    color: var(--text-1);
    font-weight: 700;
    }

    /* ── FORM FOOTER ─── */
    .form-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 16px;
    margin-top: 15px;
    }

    .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 11px;
    background: linear-gradient(135deg, var(--accent) 0%, #7c3aed 100%);
    color: #fff;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    font-family: var(--font);
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 28px color-mix(in srgb, var(--accent) 30%, transparent);
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
    }

    .btn-primary::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s;
    }

    .btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 36px color-mix(in srgb, var(--accent) 40%, transparent);
    }

    .btn-primary svg {
    width: 20px;
    height: 20px;
    }
@endsection

@section('content')

    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </span>
                Edit Toko
            </h1>
            <p>Perbarui informasi toko <strong>{{ $store->name }}</strong>.</p>
        </div>
        <a href="{{ route('stores.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 5l-7 7 7 7" />
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('stores.update', $store) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <div class="form-container">
            <div class="form-main">
                <div class="form-card">
                    <div class="form-card-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                            <span class="form-card-header-title">Informasi Toko</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-group">
                            <label class="field-label" for="name">Nama Toko <span>*</span></label>
                            <input type="text" id="name" name="name"
                                class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Nama Toko"
                                value="{{ old('name', $store->name) }}" required>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="description">Deskripsi Toko <span>*</span></label>
                            <textarea id="description" name="description"
                                class="field-input field-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}" placeholder="Deskripsi">{{ old('description', $store->description) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-card">
                    <div class="form-card-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span class="form-card-header-title">Status</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="toggle-card">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $store->is_active) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-label">Aktif</div>
                        </div>
                        <p class="field-hint">Toko yang aktif dapat mengelola produk dan kategori.</p>
                    </div>
                </div>
            </div>

            <div class="form-sidebar">
                {{-- Card: Audit Trail --}}
                <div class="form-card"
                    style="border-color: var(--accent-glow); background: color-mix(in srgb, var(--accent) 2%, var(--panel));">
                    <div class="form-card-header" style="background: transparent;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span class="form-card-header-title">Riwayat Data</span>
                        </div>
                    </div>
                    <div class="form-card-body" style="padding: 16px 24px; gap: 12px;">
                        <div class="audit-item">
                            <span class="audit-label">Dibuat:</span>
                            <span class="audit-value">{{ $store->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="audit-item">
                            <span class="audit-label">Terakhir Diubah:</span>
                            <span class="audit-value">{{ $store->updated_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <circle cx="8.5" cy="8.5" r="1.5" />
                                <polyline points="21 15 16 10 5 21" />
                            </svg>
                            <span class="form-card-header-title">Logo Toko</span>
                        </div>
                    </div>
                    <div class="form-card-body" style="gap:16px;">
                        <div id="logoContainer">
                            @if ($store->logo)
                                <div class="image-thumb" id="logo-card">
                                    <img src="{{ asset('storage/' . $store->logo) }}" id="logo-preview"
                                        onclick="openImageModal(this.src)">
                                    <div class="image-actions">
                                        <button type="button" class="img-action-btn btn-replace"
                                            onclick="document.getElementById('logo').click()"
                                            title="Ganti Logo">Ganti</button>
                                        <button type="button" class="img-action-btn btn-delete"
                                            onclick="removeExistingLogo()" title="Hapus Logo">Hapus</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Upload zone: hanya muncul jika belum ada logo --}}
                        <div class="image-upload-zone" id="uploadZone" style="{{ $store->logo ? 'display:none;' : '' }}"
                            onclick="document.getElementById('logo').click()">
                            <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor"
                                stroke-width="1.5" style="color:var(--text-4); margin-bottom:8px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" y1="3" x2="12" y2="15" />
                            </svg>
                            <div style="font-size:13px; font-weight:700; color:var(--text-2);">Unggah Logo</div>
                            <div style="font-size:11px; color:var(--text-4); margin-top:4px;">JPG, PNG, WEBP (Max 2MB)
                            </div>
                        </div>

                        {{-- Input file utama (hanya 1 elemen untuk mencegah duplikasi name/id) --}}
                        <input type="file" id="logo" name="logo" style="display:none;" accept="image/*" onchange="previewLogo(this)">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                    <polyline points="7 3 7 8 15 8" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>

    {{-- Delete Modal for Logo --}}
    <div class="modal-overlay" id="deleteLogoModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    <path d="M10 11v6M14 11v6" />
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                </svg>
            </div>
            <div class="modal-title">Hapus Logo Toko?</div>
            <div class="modal-desc">
                Logo ini akan dihapus secara permanen dari server. Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteLogoModal()">Batalkan</button>
                <form action="{{ route('stores.destroyLogo', $store) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Ya, Hapus Logo</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <div id="imageModal" class="image-modal">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <img class="image-modal-content" id="modalImage">
    </div>

    <script>
        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = src;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        function previewLogo(input) {
            const container = document.getElementById('logoContainer');
            const uploadZone = document.getElementById('uploadZone');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let card = document.getElementById('logo-card');
                    if (!card) {
                        container.innerHTML = `
                            <div class="image-thumb" id="logo-card">
                                <img src="${e.target.result}" id="logo-preview" onclick="openImageModal(this.src)">
                                <div class="image-actions">
                                    <button type="button" class="img-action-btn btn-replace" onclick="document.getElementById('logo').click()" title="Ganti Logo">Ganti</button>
                                    <button type="button" class="img-action-btn btn-delete" onclick="removeExistingLogo()" title="Hapus Logo">Hapus</button>
                                </div>
                            </div>
                        `;
                    } else {
                        document.getElementById('logo-preview').src = e.target.result;
                    }
                    uploadZone.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeExistingLogo() {
            document.getElementById('deleteLogoModal').classList.add('open');
        }

        function closeDeleteLogoModal() {
            document.getElementById('deleteLogoModal').classList.remove('open');
        }

        document.getElementById('deleteLogoModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteLogoModal();
        });
    </script>
@endpush
