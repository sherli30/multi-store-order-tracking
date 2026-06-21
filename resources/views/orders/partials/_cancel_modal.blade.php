{{-- Cancel Order Modal --}}
<div id="cancelModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon red">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>
        <h3 class="modal-title">Batalkan Pesanan</h3>
        <p class="modal-desc">Apakah Anda yakin ingin membatalkan pesanan <strong id="cancelOrderNumber">{{ isset($cancelOrder) ? '#' . $cancelOrder->order_number : '#ORD-XXXX' }}</strong>? Tindakan ini akan mengembalikan stok produk.</p>

        <form id="cancelForm" method="POST" action="{{ isset($cancelOrder) ? route('orders.cancel', $cancelOrder) : '' }}" novalidate>
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label class="form-label" style="text-transform: uppercase;">Alasan Pembatalan</label>
                <textarea name="cancel_reason" class="form-input" rows="3" placeholder="Masukkan alasan pembatalan...">{{ old('cancel_reason') }}</textarea>
                @error('cancel_reason')
                <div class="field-error" style="color: var(--danger); font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModals()">Kembali</button>
                <button type="submit" class="btn-danger">Ya, Batalkan Pesanan</button>
            </div>
        </form>
    </div>
</div>
