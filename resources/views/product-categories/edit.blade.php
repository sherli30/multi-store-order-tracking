@extends('layouts.app')

@section('title', 'Edit Kategori Produk')

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

    .toggle-sublabel {
    font-size: 12px;
    color: var(--text-3);
    margin-top: 2px;
    font-weight: 500;
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
                Edit Kategori
            </h1>
            <p>Perbarui informasi kategori <strong>{{ $productCategory->name }}</strong>.</p>
        </div>
        <a href="{{ route('product-categories.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('product-categories.update', $productCategory->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="form-container">
            <div class="form-main">
                <div class="form-card">
                    <div class="form-card-header">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                            </svg>
                            <span class="form-card-header-title">Informasi Kategori</span>
                        </div>
                    </div>

                    <div class="form-card-body">
                        {{-- Toko (Store) --}}
                        <div class="field-group">
                            <label class="field-label" for="store_id">Toko <span>*</span></label>
                            <select id="store_id" name="store_id"
                                class="field-input {{ $errors->has('store_id') ? 'is-invalid' : '' }}" required onchange="checkStoreStatus()">
                                <option value="" data-active="1" disabled selected>Pilih Toko...</option>
                                @foreach ($stores as $store)
                                    @if($store->is_active || $store->id == old('store_id', $productCategory->store_id))
                                        <option value="{{ $store->id }}" data-active="{{ $store->is_active ? '1' : '0' }}"
                                            {{ old('store_id', $productCategory->store_id) == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }} {{ !$store->is_active ? '(Non-aktif)' : '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('store_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Kategori --}}
                        <div class="field-group">
                            <label class="field-label" for="name">Nama Kategori <span>*</span></label>
                            <input type="text" id="name" name="name"
                                class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ old('name', $productCategory->name) }}" required>
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="field-group">
                            <label class="field-label" for="description">Deskripsi <span>*</span></label>
                            <textarea id="description" name="description"
                                class="field-input field-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('description', $productCategory->description) }}</textarea>
                            @error('description')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
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
                            <span class="audit-value">{{ $productCategory->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="audit-item">
                            <span class="audit-label">Terakhir Diubah:</span>
                            <span class="audit-value">{{ $productCategory->updated_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card: Status --}}
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
                        <div class="toggle-card {{ $errors->has('is_active') ? 'is-invalid' : '' }}" id="activeToggleCard" style="{{ $errors->has('is_active') ? 'border-color: var(--red);' : '' }}">
                            <label class="toggle-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="categoryActiveInput" value="1"
                                    {{ old('is_active', $productCategory->is_active) ? 'checked' : '' }} onchange="document.getElementById('categoryActiveLabel').innerText = this.checked ? 'Kategori Aktif' : 'Kategori Nonaktif'">
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-label" id="categoryActiveLabel">{{ old('is_active', $productCategory->is_active) ? 'Kategori Aktif' : 'Kategori Nonaktif' }}</div>
                        </div>
                        @error('is_active')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                        <div id="storeInactiveWarning" style="display: none; align-items: flex-start; gap: 10px; font-size: 12px; line-height: 1.5; color: var(--red); background: color-mix(in srgb, var(--red) 8%, var(--panel)); border: 1.5px solid color-mix(in srgb, var(--red) 20%, transparent); border-radius: 10px; padding: 12px 14px; text-align: left;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 16px; height: 16px; margin-top: 2px; flex-shrink: 0; color: var(--red);"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <div>
                                <span style="font-weight: 800; display: block; margin-bottom: 3px; font-size: 12.5px;">Toko Induk Non-Aktif</span>
                                Status kategori ini dikunci karena toko utamanya sedang dinonaktifkan di Kelola Toko. Silakan aktifkan toko terlebih dahulu jika ingin mengaktifkan kategori ini.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
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
        </div>

    </form>

@endsection

@push('scripts')
<script>
    function checkStoreStatus() {
        const storeSelect = document.getElementById('store_id');
        const selectedOption = storeSelect.options[storeSelect.selectedIndex];
        const warningEl = document.getElementById('storeInactiveWarning');
        const toggleCard = document.getElementById('activeToggleCard');
        const isActiveCheckbox = document.getElementById('categoryActiveInput');
        const categoryActiveLabel = document.getElementById('categoryActiveLabel');

        if (selectedOption && selectedOption.getAttribute('data-active') === '0') {
            warningEl.style.display = 'flex';
            isActiveCheckbox.checked = false;
            isActiveCheckbox.disabled = true;
            toggleCard.style.opacity = '0.55';
            toggleCard.style.pointerEvents = 'none';
            categoryActiveLabel.innerText = 'Kategori Nonaktif';
        } else {
            warningEl.style.display = 'none';
            isActiveCheckbox.disabled = false;
            toggleCard.style.opacity = '1';
            toggleCard.style.pointerEvents = 'auto';
            categoryActiveLabel.innerText = isActiveCheckbox.checked ? 'Kategori Aktif' : 'Kategori Nonaktif';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkStoreStatus();
    });
</script>
@endpush
