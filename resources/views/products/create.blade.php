@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

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
        gap: 12px;
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

    .form-card-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
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

    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
    }

    @media(max-width: 600px) {
        .field-row {
            grid-template-columns: 1fr;
        }
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

    /* ── DYNAMIC TABLE ─── */
    .dynamic-table-wrapper {
        overflow-x: auto;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        box-shadow: 0 2px 4px color-mix(in srgb, var(--text-1) 3%, transparent);
    }

    .dynamic-table {
        width: 100%;
        border-collapse: collapse;
    }

    .dynamic-table th {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-2);
        text-transform: uppercase;
        text-align: left;
        padding: 14px 16px;
        background: linear-gradient(135deg, var(--surface-2) 0%, color-mix(in srgb, var(--accent) 1%, var(--surface-2)) 100%);
        border-bottom: 1.5px solid var(--border);
        letter-spacing: 0.4px;
    }

    .dynamic-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
    }

    .dynamic-table tr:last-child td {
        border-bottom: none;
    }

    .dynamic-table tr:hover td {
        background: color-mix(in srgb, var(--accent) 2%, transparent);
    }

    /* ── BUTTONS (ADD/REMOVE) ─── */
    .btn-add-row {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border: 2px dashed var(--border);
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-2);
        background: transparent;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        margin-top: 8px;
        width: fit-content;
        letter-spacing: 0.2px;
    }

    .btn-add-row:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: color-mix(in srgb, var(--accent) 6%, transparent);
        transform: translateY(-2px);
    }

    .btn-remove-row {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--red);
        border: 1.5px solid var(--border);
        border-radius: 9px;
        background: var(--panel);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }

    .btn-remove-row:hover {
        background: var(--red);
        color: #fff;
        border-color: var(--red);
        transform: scale(1.05);
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

    /* ── BUTTONS ─── */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 11px;
        width: 100%;
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

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .btn-primary svg {
        width: 20px;
        height: 20px;
    }

    /* ── IMAGE UPLOAD ─── */
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

    .image-upload-icon {
        width: 52px;
        height: 52px;
        color: var(--accent);
        margin: 0 auto 14px;
        opacity: 0.8;
        transition: all 0.3s;
    }

    .image-upload-zone:hover .image-upload-icon {
        opacity: 1;
        transform: scale(1.1);
    }

    /* ── IMAGE THUMBNAILS ─── */
    .image-thumb {
        position: relative;
        width: 110px;
        height: 110px;
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
    .image-modal-close:hover,
    .image-modal-close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* ── IMAGE ACTIONS ─── */
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
        z-index: 20;
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



    /* ── Delete Modal ─── */
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
@endsection

@section('content')

    <div class="page-header">
        <div class="page-header-left">
            <h1>
                <span class="page-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </span>
                Tambah Produk
            </h1>
            <p>Buat entitas produk baru dengan variasi dan spesifikasi lengkap.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 5l-7 7 7 7" />
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm" novalidate>
        @csrf

        <div class="form-container">
            <div class="form-main">

                {{-- Card: Informasi Dasar --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                            <span class="form-card-header-title">Informasi Produk</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-group">
                            <label class="field-label" for="name">Nama Produk <span>*</span></label>
                            <input type="text" id="name" name="name" class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Contoh: Kopi Bubuk Arabika 250gr" value="{{ old('name') }}" required autofocus>
                            @error('name') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="sku">SKU <span>*</span></label>
                            <input type="text" id="sku" name="sku" class="field-input {{ $errors->has('sku') ? 'is-invalid' : '' }}" placeholder="Contoh: KBA-250" value="{{ old('sku') }}" required>
                            @error('sku') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="store_id">Toko Terkait <span>*</span></label>
                            <select id="store_id" name="store_id" class="field-input {{ $errors->has('store_id') ? 'is-invalid' : '' }}" required onchange="checkStoreStatus()">
                                <option value="" data-active="1" disabled selected>Pilih Toko...</option>
                                @foreach($stores as $store)
                                    @if($store->is_active || $store->id == old('store_id'))
                                        <option value="{{ $store->id }}" data-active="{{ $store->is_active ? '1' : '0' }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }} {{ !$store->is_active ? '(Non-aktif)' : '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('store_id') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="category_id">Kategori Produk <span>*</span></label>
                            <select id="category_id" name="category_id" class="field-input {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required disabled onchange="checkStoreStatus()">
                                <option value="" disabled selected>Pilih Toko Dahulu...</option>
                            </select>
                            @error('category_id') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Card: Harga, Stok & Berat --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                            <span class="form-card-header-title">Harga, Stok & Berat</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-row">
                            <div class="field-group">
                                <label class="field-label">Harga Jual (Rp) <span>*</span></label>
                                <div style="position:relative;">
                                    <span style="position:absolute; left:16px; top:50%; transform:translateY(-50%); font-size:14px; font-weight:700; color:var(--text-3); z-index:10; pointer-events:none;">Rp</span>
                                    <input type="text" inputmode="numeric" name="price" id="single_price" class="field-input price-input {{ $errors->has('price') ? 'is-invalid' : '' }}" style="padding-left:42px;" placeholder="0" required value="{{ old('price') }}">
                                </div>
                                @error('price') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                            </div>
                            <div class="field-group">
                                <label class="field-label">Stok Awal <span>*</span></label>
                                <input type="number" name="stock" id="single_stock" class="field-input {{ $errors->has('stock') ? 'is-invalid' : '' }}" placeholder="0" min="0" required value="{{ old('stock') }}">
                                @error('stock') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                            </div>
                            <div class="field-group">
                                <label class="field-label">Berat (kg) <span>*</span></label>
                                <input type="number" step="0.01" name="weight" id="single_weight" class="field-input {{ $errors->has('weight') ? 'is-invalid' : '' }}" placeholder="Contoh: 0.5 kg" min="0" required value="{{ old('weight') }}">
                                @error('weight') <span class="field-hint" style="color:var(--red);">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Deskripsi --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            <span class="form-card-header-title">Deskripsi Produk</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div id="descriptionsContainer" style="display:flex; flex-direction:column; gap:20px;">
                            @if(old('descriptions'))
                                @foreach(old('descriptions') as $index => $desc)
                                    <div class="toggle-card" style="flex-direction:column; align-items:stretch;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                            <input type="text" name="descriptions[{{ $index }}][title]" class="field-input {{ ($errors->has('descriptions.'.$index.'.title') || $errors->has('descriptions')) ? 'is-invalid' : '' }}" style="width:70%;" placeholder="Judul * (Cth: Keunggulan)" required value="{{ $desc['title'] ?? '' }}">
                                            <button type="button" class="btn-remove-row" onclick="this.parentElement.parentElement.remove()">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </div>
                                        <textarea name="descriptions[{{ $index }}][content]" class="field-input field-textarea {{ ($errors->has('descriptions.'.$index.'.content') || $errors->has('descriptions')) ? 'is-invalid' : '' }}" placeholder="Isi deskripsi bagian ini... *" required>{{ $desc['content'] ?? '' }}</textarea>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn-add-row" onclick="addDescriptionRow()">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Blok Deskripsi
                        </button>
                    </div>
                </div>

            </div>

            <div class="form-sidebar">

                {{-- Card: Status & Publikasi --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <span class="form-card-header-title">Status</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="toggle-card {{ $errors->has('is_active') ? 'is-invalid' : '' }}" id="activeToggleCard" style="{{ $errors->has('is_active') ? 'border-color: var(--red);' : '' }}">
                            <label class="toggle-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="productActiveInput" value="1"
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }} onchange="updateActiveStatusLabel(this)">
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-label" id="productActiveLabel">{{ old('is_active', '1') == '1' ? 'Produk Aktif' : 'Produk Nonaktif' }}</div>
                        </div>
                        <div class="toggle-card {{ $errors->has('is_featured') ? 'is-invalid' : '' }}" style="{{ $errors->has('is_featured') ? 'border-color: var(--red);' : '' }}">
                            <label class="toggle-switch">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" id="productFeaturedInput" value="1"
                                    {{ old('is_featured') ? 'checked' : '' }} onchange="document.getElementById('productFeaturedLabel').innerText = this.checked ? 'Produk Unggulan' : 'Produk Reguler'">
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-label" id="productFeaturedLabel">{{ old('is_featured') ? 'Produk Unggulan' : 'Produk Reguler' }}</div>
                        </div>
                        <div id="storeInactiveWarning" style="display: none; align-items: flex-start; gap: 10px; font-size: 12px; line-height: 1.5; color: var(--red); background: color-mix(in srgb, var(--red) 8%, var(--panel)); border: 1.5px solid color-mix(in srgb, var(--red) 20%, transparent); border-radius: 10px; padding: 12px 14px; text-align: left; margin-bottom: 8px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 16px; height: 16px; margin-top: 2px; flex-shrink: 0; color: var(--red);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <div>
                                <span style="font-weight: 800; display: block; margin-bottom: 3px; font-size: 12.5px;">Toko Induk Non-Aktif</span>
                                Status produk ini dikunci karena toko utamanya sedang dinonaktifkan di Kelola Toko. Silakan aktifkan toko terlebih dahulu jika ingin mengaktifkan produk ini.
                            </div>
                        </div>
                        <div id="categoryInactiveWarning" style="display: none; align-items: flex-start; gap: 10px; font-size: 12px; line-height: 1.5; color: var(--red); background: color-mix(in srgb, var(--red) 8%, var(--panel)); border: 1.5px solid color-mix(in srgb, var(--red) 20%, transparent); border-radius: 10px; padding: 12px 14px; text-align: left;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 16px; height: 16px; margin-top: 2px; flex-shrink: 0; color: var(--red);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <div>
                                <span style="font-weight: 800; display: block; margin-bottom: 3px; font-size: 12.5px;">Kategori Non-Aktif</span>
                                Status produk ini dikunci karena kategori utamanya sedang dinonaktifkan. Silakan aktifkan kategori terlebih dahulu jika ingin mengaktifkan produk ini.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Galeri Foto --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span class="form-card-header-title">Galeri Foto</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-group">
                            <label class="field-label">Foto Produk <span>*</span></label>
                            <div class="image-upload-zone" onclick="document.getElementById('images').click()" style="{{ ($errors->has('images') || $errors->has('images.*')) ? 'border-color: var(--red);' : '' }}">
                                <div class="image-upload-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                </div>
                                <div style="font-size:13px; font-weight:700; color:var(--text-1); margin-bottom:4px;">Klik untuk Unggah</div>
                                <div style="font-size:11px; color:var(--text-4);">PNG, JPG, WebP (Maks. 2MB)</div>
                                <input type="file" id="images" name="images[]" style="display:none;" accept="image/*" multiple onchange="previewImages(this)">
                            </div>
                            <div id="imagePreviewContainer" style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;"></div>
                            <div id="imageCounter" class="field-hint" style="margin-top:8px; display:none;">0 file terpilih</div>
                        </div>
                    </div>
                </div>

                {{-- Card: Spesifikasi --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                            <span class="form-card-header-title">Spesifikasi</span>
                        </div>
                    </div>
                    <div class="form-card-body" style="gap:16px;">
                        <div id="specificationsContainer" style="display:flex; flex-direction:column; gap:12px;">
                            @if(old('specifications'))
                                @foreach(old('specifications') as $index => $spec)
                                    <div style="display:flex; gap:8px;">
                                        <input type="text" name="specifications[{{ $index }}][name]" class="field-input {{ ($errors->has('specifications.'.$index.'.name') || $errors->has('specifications')) ? 'is-invalid' : '' }}" style="flex:1;" placeholder="Label *" required value="{{ $spec['name'] ?? '' }}">
                                        <input type="text" name="specifications[{{ $index }}][value]" class="field-input {{ ($errors->has('specifications.'.$index.'.value') || $errors->has('specifications')) ? 'is-invalid' : '' }}" style="flex:1;" placeholder="Nilai *" required value="{{ $spec['value'] ?? '' }}">
                                        <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()" style="flex-shrink:0;">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn-add-row" onclick="addSpecificationRow()" style="width:100%; justify-content:center;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Spesifikasi
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Simpan Produk
                </button>

            </div>
        </div>

    </form>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <img class="image-modal-content" id="modalImage">
    </div>

    {{-- Delete Modal for Image --}}
    <div class="modal-overlay" id="deleteImageModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    <path d="M10 11v6M14 11v6" />
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                </svg>
            </div>
            <div class="modal-title">Hapus Foto?</div>
            <div class="modal-desc">
                Foto ini belum disimpan di server dan akan dihapus dari pilihan. Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteImageModal()">Batalkan</button>
                <button type="button" class="btn-danger" id="confirmDeleteImageBtn">Ya, Hapus Foto</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // ── IMAGE PREVIEW, COUNTER, REPLACE, AND DELETE ──
    let selectedFiles = [];

    function previewImages(input) {
        if (input.files && input.files.length > 0) {
            selectedFiles = Array.from(input.files);
            renderPreviews();
        }
    }

    function renderPreviews() {
        const counter = document.getElementById('imageCounter');
        const container = document.getElementById('imagePreviewContainer');
        const input = document.getElementById('images');

        container.innerHTML = '';

        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;

        if (selectedFiles.length > 0) {
            counter.style.display = 'block';
            counter.textContent = `${selectedFiles.length} file dipilih`;

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-thumb';
                    div.style.position = 'relative';

                    div.innerHTML = `
                        <img src="${e.target.result}" onclick="event.stopPropagation(); openImageModal('${e.target.result}')">
                        <div class="image-actions">
                            <button type="button" class="img-action-btn btn-replace" onclick="event.stopPropagation(); replaceSingleImage(${index})" title="Ganti Foto">Ganti</button>
                            <button type="button" class="img-action-btn btn-delete" onclick="event.stopPropagation(); deleteSingleImage(${index})" title="Hapus Foto">Hapus</button>
                        </div>
                    `;
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        } else {
            counter.style.display = 'none';
        }
    }

    let imageToDeleteIndex = null;

    window.deleteSingleImage = function(index) {
        imageToDeleteIndex = index;
        document.getElementById('deleteImageModal').classList.add('open');
    }

    window.closeDeleteImageModal = function() {
        document.getElementById('deleteImageModal').classList.remove('open');
        imageToDeleteIndex = null;
    }

    document.getElementById('confirmDeleteImageBtn').addEventListener('click', function() {
        if (imageToDeleteIndex !== null) {
            selectedFiles.splice(imageToDeleteIndex, 1);
            renderPreviews();
            closeDeleteImageModal();
        }
    });

    document.getElementById('deleteImageModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteImageModal();
    });

    window.replaceSingleImage = function(index) {
        const tempInput = document.createElement('input');
        tempInput.type = 'file';
        tempInput.accept = 'image/*';
        tempInput.onchange = function(e) {
            if (e.target.files && e.target.files[0]) {
                selectedFiles[index] = e.target.files[0];
                renderPreviews();
            }
        };
        tempInput.click();
    }

    // ── STORE STATUS CHECK ──
    function checkStoreStatus() {
        const storeSelect = document.getElementById('store_id');
        const categorySelect = document.getElementById('category_id');
        const selectedStore = storeSelect.options[storeSelect.selectedIndex];
        const selectedCategory = categorySelect.options[categorySelect.selectedIndex];
        
        const warningEl = document.getElementById('storeInactiveWarning');
        const categoryWarningEl = document.getElementById('categoryInactiveWarning');
        const toggleCard = document.getElementById('activeToggleCard');
        const isActiveCheckbox = document.getElementById('productActiveInput');
        const productActiveLabel = document.getElementById('productActiveLabel');

        if (!isActiveCheckbox || !toggleCard) return;

        let isStoreInactive = selectedStore && selectedStore.getAttribute('data-active') === '0';
        let isCategoryInactive = selectedCategory && selectedCategory.getAttribute('data-active') === '0';

        if (warningEl) warningEl.style.display = isStoreInactive ? 'flex' : 'none';
        if (categoryWarningEl) categoryWarningEl.style.display = (!isStoreInactive && isCategoryInactive) ? 'flex' : 'none';

        if (isStoreInactive || isCategoryInactive) {
            isActiveCheckbox.checked = false;
            isActiveCheckbox.disabled = true;
            toggleCard.style.opacity = '0.55';
            toggleCard.style.pointerEvents = 'none';
            productActiveLabel.innerText = 'Produk Nonaktif';
        } else {
            isActiveCheckbox.disabled = false;
            toggleCard.style.opacity = '1';
            toggleCard.style.pointerEvents = 'auto';
            productActiveLabel.innerText = isActiveCheckbox.checked ? 'Produk Aktif' : 'Produk Nonaktif';
        }
    }
    window.checkStoreStatus = checkStoreStatus;

    function updateActiveStatusLabel(checkbox) {
        document.getElementById('productActiveLabel').innerText = checkbox.checked ? 'Produk Aktif' : 'Produk Nonaktif';
    }
    window.updateActiveStatusLabel = updateActiveStatusLabel;

    // ── STORE -> CATEGORY DYNAMIC DROPDOWN ──
    document.addEventListener("DOMContentLoaded", function() {
        const storeSelect = document.getElementById('store_id');
        const categorySelect = document.getElementById('category_id');
        const oldCategoryId = "{{ old('category_id') }}";

        function loadCategories(storeId, selectedCategoryId = null) {
            categorySelect.innerHTML = '<option value="" disabled selected>Memuat...</option>';
            categorySelect.disabled = true;

            const url = "{{ route('stores.categories', ':id') }}".replace(':id', storeId);

            fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                categorySelect.innerHTML = '<option value="" disabled selected>Pilih Kategori...</option>';
                if (data.length > 0) {
                    let hasOptions = false;
                    data.forEach(cat => {
                        if (cat.is_active || selectedCategoryId == cat.id) {
                            hasOptions = true;
                            const option = document.createElement('option');
                            option.value = cat.id;
                            option.setAttribute('data-active', cat.is_active ? '1' : '0');
                            option.textContent = cat.name + (cat.is_active ? '' : ' (Non-aktif)');
                            if (selectedCategoryId == cat.id) option.selected = true;
                            categorySelect.appendChild(option);
                        }
                    });
                    
                    if (hasOptions) {
                        categorySelect.disabled = false;
                    } else {
                        categorySelect.innerHTML = '<option value="" disabled selected>Tidak ada kategori aktif untuk toko ini</option>';
                    }
                } else {
                    categorySelect.innerHTML = '<option value="" disabled selected>Tidak ada kategori aktif untuk toko ini</option>';
                }
                // Trigger check to validate if the newly loaded category affects the active toggle
                checkStoreStatus();
            })
            .catch(err => {
                categorySelect.innerHTML = '<option value="" disabled selected>Gagal memuat kategori</option>';
                if (typeof showToast === 'function') {
                    showToast('Gagal memuat kategori. Periksa koneksi jaringan Anda.', 'error');
                }
            });
        }

        checkStoreStatus();
        if (storeSelect.value) {
            loadCategories(storeSelect.value, oldCategoryId);
        }

        storeSelect.addEventListener('change', function() {
            checkStoreStatus();
            if (this.value) {
                loadCategories(this.value);
            } else {
                categorySelect.innerHTML = '<option value="" disabled selected>Pilih Toko Dahulu...</option>';
                categorySelect.disabled = true;
            }
        });
    });


    let descIndex = {{ is_array(old('descriptions')) ? count(old('descriptions')) : 0 }};
    function addDescriptionRow() {
        const container = document.getElementById('descriptionsContainer');
        const div = document.createElement('div');
        div.className = 'toggle-card';
        div.style.flexDirection = 'column';
        div.style.alignItems = 'stretch';
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <input type="text" name="descriptions[${descIndex}][title]" class="field-input" style="width:70%;" placeholder="Judul * (Cth: Keunggulan)" required>
                <button type="button" class="btn-remove-row" onclick="this.parentElement.parentElement.remove()">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </div>
            <textarea name="descriptions[${descIndex}][content]" class="field-input field-textarea" placeholder="Isi deskripsi bagian ini... *" required></textarea>
        `;
        container.appendChild(div);
        descIndex++;
    }

    let specIndex = {{ is_array(old('specifications')) ? count(old('specifications')) : 0 }};
    function addSpecificationRow() {
        const container = document.getElementById('specificationsContainer');
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.gap = '8px';
        div.innerHTML = `
            <input type="text" name="specifications[${specIndex}][name]" class="field-input" style="flex:1;" placeholder="Label *" required>
            <input type="text" name="specifications[${specIndex}][value]" class="field-input" style="flex:1;" placeholder="Nilai *" required>
            <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()" style="flex-shrink:0;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
        `;
        container.appendChild(div);
        specIndex++;
    }

    // Modal Image Functions
    function openImageModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        modal.style.display = 'flex';
        modalImg.src = src;
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    // Init rows
    @if(!old('descriptions'))
        addDescriptionRow();
    @endif
    @if(!old('specifications'))
        addSpecificationRow();
    @endif

    // ── PRICE FORMATTING (Indonesian: dot as thousands separator) ──
    function fmtPrice(val) {
        const n = String(val).replace(/\D/g, '');
        return n ? n.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
    }
    function bindPriceInput(el) {
        if (el.dataset.pricebound) return;
        el.dataset.pricebound = '1';
        if (el.value) el.value = fmtPrice(el.value);
        el.addEventListener('input', function () {
            const pos = this.selectionStart;
            const raw = this.value.replace(/\D/g, '');
            const fmt = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            const diff = fmt.length - this.value.length;
            this.value = fmt;
            this.setSelectionRange(pos + diff, pos + diff);
        });
    }
    function initPriceInputs() {
        document.querySelectorAll('.price-input').forEach(bindPriceInput);
    }
    document.querySelector('form').addEventListener('submit', function () {
        this.querySelectorAll('.price-input').forEach(function (el) {
            el.value = el.value.replace(/\./g, '');
        });
    });
    new MutationObserver(initPriceInputs).observe(document.body, { childList: true, subtree: true });
    initPriceInputs();
</script>
@endpush
