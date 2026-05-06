@extends('layouts.app')

@section('title', 'Tambah Produk Baru')

@section('styles')
    /* â”€â”€ PAGE HEADER â”€â”€â”€ */
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

    /* â”€â”€ BUTTONS â”€â”€â”€ */
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

    /* â”€â”€ FORM LAYOUT â”€â”€â”€ */
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

    /* â”€â”€ FORM CARDS â”€â”€â”€ */
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

    /* â”€â”€ FORM FIELDS â”€â”€â”€ */
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

    /* â”€â”€ DYNAMIC TABLE â”€â”€â”€ */
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

    /* â”€â”€ BUTTONS (ADD/REMOVE) â”€â”€â”€ */
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

    /* â”€â”€ TOGGLE CARD â”€â”€â”€ */
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

    /* â”€â”€ FORM FOOTER â”€â”€â”€ */
    .form-footer {
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:16px;
    margin-top:15px;
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

    .btn-primary:active {
        transform: translateY(-1px);
    }

    .btn-primary svg {
        width: 20px;
        height: 20px;
    }

    /* â”€â”€ IMAGE UPLOAD â”€â”€â”€ */
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

    /* â”€â”€ IMAGE THUMBNAILS â”€â”€â”€ */
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

    /* â”€â”€ IMAGE MODAL â”€â”€â”€ */
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
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            <span class="form-card-header-title">Informasi Produk</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-row">
                            <div class="field-group">
                                <label class="field-label" for="store_id">Toko Terkait <span>*</span></label>
                                <select id="store_id" name="store_id" class="field-input" required>
                                    <option value="" disabled selected>Pilih Toko...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="category_id">Kategori Produk <span>*</span></label>
                                <select id="category_id" name="category_id" class="field-input" required disabled>
                                    <option value="" disabled selected>Pilih Toko Dahulu...</option>
                                </select>
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label" for="name">Nama Produk <span>*</span></label>
                            <input type="text" id="name" name="name" class="field-input" placeholder="Contoh: Kopi Bubuk Arabika 250gr" value="{{ old('name') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Card: Variasi --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                            <span class="form-card-header-title">Harga & Stok</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="toggle-card">
                            <label class="toggle-switch">
                                <input type="checkbox" id="has_variants" name="has_variants" value="1" {{ old('has_variants') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <div>
                                <div class="toggle-label">Gunakan Variasi Produk</div>
                                <div class="field-hint">Aktifkan jika produk memiliki pilihan ukuran, warna, atau tipe berbeda.</div>
                            </div>
                        </div>

                        {{-- Single Product --}}
                        <div id="singleProductConfig">
                            <div class="field-row">
                                <div class="field-group">
                                    <label class="field-label">Harga Jual (Rp) <span>*</span></label>
                                    <div style="position:relative;">
                                        <span style="position:absolute; left:16px; top:50%; transform:translateY(-50%); font-size:14px; font-weight:700; color:var(--text-3); z-index:10; pointer-events:none;">Rp</span>
                                        <input type="text" inputmode="numeric" name="price" id="single_price" class="field-input price-input" style="padding-left:42px;" placeholder="0" value="{{ old('price') }}">
                                    </div>
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Stok Awal <span>*</span></label>
                                    <input type="number" name="stock" id="single_stock" class="field-input" placeholder="0" min="0" value="{{ old('stock', 0) }}">
                                </div>
                                <div class="field-group">
                                    <label class="field-label">Berat (Gram) <span>*</span></label>
                                    <input type="number" name="weight" id="single_weight" class="field-input" placeholder="0" min="0" value="{{ old('weight') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Multi Variants --}}
                        <div id="variantsConfig" style="display: none;">
                            <div class="dynamic-table-wrapper">
                                <table class="dynamic-table" id="variantsTable">
                                    <thead>
                                        <tr>
                                            <th>Nama Varian <span style="color:var(--red);">*</span></th>
                                            <th>SKU <span style="color:var(--text-4); font-weight:normal;">(Opsional)</span></th>
                                            <th>Harga (Rp) <span style="color:var(--red);">*</span></th>
                                            <th>Stok <span style="color:var(--red);">*</span></th>
                                            <th>Berat (Gr) <span style="color:var(--red);">*</span></th>
                                            <th style="width:50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(old('variants'))
                                            @foreach(old('variants') as $index => $variant)
                                                <tr>
                                                    <td><input type="text" name="variants[{{ $index }}][name]" class="field-input" required placeholder="Cth: Merah / XL" value="{{ $variant['name'] ?? '' }}"></td>
                                                    <td><input type="text" name="variants[{{ $index }}][sku]" class="field-input" placeholder="Opsional" value="{{ $variant['sku'] ?? '' }}"></td>
                                                    <td><input type="text" inputmode="numeric" name="variants[{{ $index }}][price]" class="field-input price-input" required placeholder="0" value="{{ $variant['price'] ?? '' }}"></td>
                                                    <td><input type="number" name="variants[{{ $index }}][stock]" class="field-input" required min="0" placeholder="0" value="{{ $variant['stock'] ?? '' }}"></td>
                                                    <td><input type="number" name="variants[{{ $index }}][weight]" class="field-input" required min="0" placeholder="0" value="{{ $variant['weight'] ?? '' }}"></td>
                                                    <td>
                                                        <button type="button" class="btn-remove-row" onclick="this.closest('tr').remove()">
                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn-add-row" onclick="addVariantRow()">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Tambah Varian Baru
                            </button>
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
                                            <input type="text" name="descriptions[{{ $index }}][title]" class="field-input" style="width:70%;" placeholder="Judul * (Cth: Keunggulan)" required value="{{ $desc['title'] ?? '' }}">
                                            <button type="button" class="btn-remove-row" onclick="this.parentElement.parentElement.remove()">
                                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </div>
                                        <textarea name="descriptions[{{ $index }}][content]" class="field-input field-textarea" placeholder="Isi deskripsi bagian ini... *" required>{{ $desc['content'] ?? '' }}</textarea>
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
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span class="form-card-header-title">Status</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="toggle-card">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-label">Aktif / Publikasi</div>
                        </div>
                        <div class="toggle-card">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-label">Unggulan (Home)</div>
                        </div>
                        <p class="field-hint">Produk yang tidak aktif akan disembunyikan dari daftar belanja pelanggan.</p>
                    </div>
                </div>

                {{-- Card: Media --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <span class="form-card-header-title">Media</span>
                        </div>
                    </div>
                    <div class="form-card-body">
                        <div class="field-group">
                            <label class="field-label">Foto Produk <span>*</span></label>
                            <div class="image-upload-zone" onclick="document.getElementById('images').click()">
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
                                        <input type="text" name="specifications[{{ $index }}][name]" class="field-input" style="flex:1;" placeholder="Label *" required value="{{ $spec['name'] ?? '' }}">
                                        <input type="text" name="specifications[{{ $index }}][value]" class="field-input" style="flex:1;" placeholder="Nilai *" required value="{{ $spec['value'] ?? '' }}">
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

                {{-- Card: Packing --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-header-left">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                            <span class="form-card-header-title">Opsi Packing <span style="color:var(--text-4); font-size:12px; font-weight:normal; margin-left:4px;">(Opsional)</span></span>
                        </div>
                    </div>
                    <div class="form-card-body" style="gap:12px;">
                        <div id="packingContainer" style="display:flex; flex-direction:column; gap:12px;">
                            @if(old('packing_options'))
                                @foreach(old('packing_options') as $index => $pack)
                                    <div style="display:flex; gap:8px;">
                                        <input type="text" name="packing_options[{{ $index }}][name]" class="field-input" style="flex:1;" placeholder="Nama (Opsional)" value="{{ $pack['name'] ?? '' }}">
                                        <input type="text" inputmode="numeric" name="packing_options[{{ $index }}][extra_price]" class="field-input price-input" style="flex:1;" placeholder="Harga (Opsional)" value="{{ $pack['extra_price'] ?? '' }}">
                                        <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()" style="flex-shrink:0;">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn-add-row" onclick="addPackingRow()" style="width:100%; justify-content:center;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Tambah Packing
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="form-footer">
            <button type="submit" class="btn-primary">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:8px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Produk
            </button>
        </div>

    </form>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <img class="image-modal-content" id="modalImage">
    </div>

@endsection

@push('scripts')
<script>
    // â”€â”€ IMAGE PREVIEW & COUNTER â”€â”€
    function previewImages(input) {
        const counter = document.getElementById('imageCounter');
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        if (input.files && input.files.length > 0) {
            counter.style.display = 'block';
            counter.textContent = `${input.files.length} file dipilih`;

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-thumb';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.onclick = function(ev) {
                        ev.stopPropagation(); // prevent triggering file upload if container wraps it
                        openImageModal(e.target.result);
                    };

                    div.appendChild(img);
                    container.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        } else {
            counter.style.display = 'none';
        }
    }

    // â”€â”€ STORE -> CATEGORY DYNAMIC DROPDOWN â”€â”€
    document.addEventListener("DOMContentLoaded", function() {
        const storeSelect = document.getElementById('store_id');
        const categorySelect = document.getElementById('category_id');
        const oldCategoryId = "{{ old('category_id') }}";

        function loadCategories(storeId, selectedCategoryId = null) {
            console.log('Fetching categories for store:', storeId);
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
                console.log('Categories data received:', data);
                categorySelect.innerHTML = '<option value="" disabled selected>Pilih Kategori...</option>';
                if(data.length > 0) {
                    categorySelect.disabled = false;
                    data.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id;
                        option.textContent = cat.name;
                        if (selectedCategoryId == cat.id) option.selected = true;
                        categorySelect.appendChild(option);
                    });
                } else {
                    categorySelect.innerHTML = '<option value="" disabled selected>Tidak ada kategori aktif</option>';
                }
            })
            .catch(err => {
                console.error('Error loading categories:', err);
                categorySelect.innerHTML = '<option value="" disabled selected>Gagal memuat kategori</option>';
            });
        }

        // Trigger on initial load if store is selected (e.g. after validation error)
        if (storeSelect.value) {
            loadCategories(storeSelect.value, oldCategoryId);
        }

        storeSelect.addEventListener('change', function() {
            if (this.value) {
                loadCategories(this.value);
            } else {
                categorySelect.innerHTML = '<option value="" disabled selected>Pilih Toko Dahulu...</option>';
                categorySelect.disabled = true;
            }
        });
    });

    // â”€â”€ VARIANT TOGGLE â”€â”€
    const hasVariantsCheckbox = document.getElementById('has_variants');
    const singleConfig = document.getElementById('singleProductConfig');
    const variantsConfig = document.getElementById('variantsConfig');
    const inputsToToggle = ['single_price', 'single_stock', 'single_weight'];

    function toggleVariants() {
        const isChecked = hasVariantsCheckbox.checked;
        singleConfig.style.display = isChecked ? 'none' : 'block';
        variantsConfig.style.display = isChecked ? 'block' : 'none';

        inputsToToggle.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.required = !isChecked;
        });

        if(isChecked && document.querySelector('#variantsTable tbody').children.length === 0) {
            addVariantRow();
        }
    }
    hasVariantsCheckbox.addEventListener('change', toggleVariants);
    toggleVariants();

    // â”€â”€ DYNAMIC ROWS â”€â”€
    let variantIndex = {{ is_array(old('variants')) ? count(old('variants')) : 0 }};
    function addVariantRow() {
        const tbody = document.querySelector('#variantsTable tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="variants[${variantIndex}][name]" class="field-input" required placeholder="Cth: Merah / XL"></td>
            <td><input type="text" name="variants[${variantIndex}][sku]" class="field-input" placeholder="Opsional"></td>
            <td><input type="text" inputmode="numeric" name="variants[${variantIndex}][price]" class="field-input price-input" required placeholder="0"></td>
            <td><input type="number" name="variants[${variantIndex}][stock]" class="field-input" required min="0" placeholder="0"></td>
            <td><input type="number" name="variants[${variantIndex}][weight]" class="field-input" required min="0" placeholder="0"></td>
            <td>
                <button type="button" class="btn-remove-row" onclick="this.closest('tr').remove()">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        variantIndex++;
    }

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

    let packingIndex = {{ is_array(old('packing_options')) ? count(old('packing_options')) : 0 }};
    function addPackingRow() {
        const container = document.getElementById('packingContainer');
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.gap = '8px';
        div.innerHTML = `
            <input type="text" name="packing_options[${packingIndex}][name]" class="field-input" style="flex:1;" placeholder="Nama (Opsional)">
            <input type="text" inputmode="numeric" name="packing_options[${packingIndex}][extra_price]" class="field-input price-input" style="flex:1;" placeholder="Harga (Opsional)">
            <button type="button" class="btn-remove-row" onclick="this.parentElement.remove()" style="flex-shrink:0;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
        `;
        container.appendChild(div);
        packingIndex++;
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
    // â”€â”€ PRICE FORMATTING (Indonesian: dot as thousands separator) â”€â”€
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
    // Strip dots before submit so server gets clean integers
    document.querySelector('form').addEventListener('submit', function () {
        this.querySelectorAll('.price-input').forEach(function (el) {
            el.value = el.value.replace(/\./g, '');
        });
    });
    // Use MutationObserver so dynamically added rows also get formatted
    new MutationObserver(initPriceInputs).observe(document.body, { childList: true, subtree: true });
    initPriceInputs();
</script>
@endpush


