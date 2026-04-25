@extends('layouts.app')

@section('title', 'Manajemen Pengiriman')

@section('styles')
.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.page-header-left h1 {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-1);
    letter-spacing: -0.02em;
    margin-bottom: 4px;
}
.page-header-left p {
    font-size: 13.5px;
    color: var(--text-3);
}

.table-card {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

th {
    background: var(--surface);
    padding: 12px 18px;
    text-align: left;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-2);
    border-bottom: 1px solid var(--border-2);
    white-space: nowrap;
}

td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

tr:hover { background: var(--surface); }

.badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
}
.badge-warning { background: var(--yellow-dim); color: var(--yellow); border: 1px solid rgba(245, 158, 11, 0.2); }
.badge-blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
.badge-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(16, 185, 129, 0.2); }
.badge-gray { background: var(--surface-2); color: var(--text-3); border: 1px solid var(--border); }

.btn-sm {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 7px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    cursor: pointer;
    text-decoration: none;
}
.btn-sm:hover { border-color: var(--border-2); color: var(--text-1); }

/* Modal Styles */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-content {
    background: var(--panel);
    width: 100%;
    max-width: 450px;
    border-radius: 14px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    animation: modalfade 0.2s ease-out;
}

@keyframes modalfade {
    from { opacity: 0; transform: translateY(15px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--surface);
}

.modal-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--text-1);
}

.modal-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 6px;
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border);
    border-radius: 9px;
    font-family: var(--font);
    font-size: 13px;
    color: var(--text-1);
    background: #fff;
    outline: none;
    box-sizing: border-box;
    transition: all 0.2s;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-glow);
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: var(--surface);
}

.btn-primary {
    background: var(--accent);
    color: #fff;
    border: none;
    padding: 9px 18px;
    border-radius: 9px;
    font-weight: 600;
    cursor: pointer;
    font-size:13px;
}
.btn-primary:hover { background: #4f51e8; }
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Update Status Pengiriman</h1>
        <p>Kelola dan update status nomor resi pengiriman</p>
    </div>
    <div class="page-header-right">
        <a href="{{ route('deliveries.scan') }}" class="btn-sm" style="background:var(--accent); color:#fff; border:none; padding:8px 16px;">
            <svg viewBox="0 0 24 24" fill="none" width="16" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Mode Scanner Terpusat
        </a>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Pesanan</th>
                    <th>Kurir & Resi</th>
                    <th>Status Gudang</th>
                    <th>Terakhir Update</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                <tr>
                    <td style="color:var(--text-4); font-weight:600;">{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
                    <td>
                        <div style="font-weight:700; color:var(--text-1);">{{ $order->order_number }}</div>
                        <div style="font-size:11.5px; color:var(--text-3); margin-top:3px;">{{ $order->customer_name }}</div>
                    </td>
                    <td>
                        @if($order->shipping_courier || $order->tracking_number)
                            <div style="font-weight:700; font-size:13px; color:var(--text-1);">{{ $order->shipping_courier ?: 'Kurir Internal' }}</div>
                            <div style="font-family:var(--mono); font-size:12px; color:var(--accent); margin-top:3px; font-weight:600; padding:3px 8px; background:var(--accent-dim); border-radius:4px; display:inline-block;">{{ $order->tracking_number ?: 'Tanpa Resi' }}</div>
                        @else
                            <span style="color:var(--text-4); font-size:12px; font-weight:500;">Menunggu input...</span>
                        @endif
                    </td>
                    <td>
                        @if($order->status === 'processing')
                            <span class="badge badge-warning">Proses Kemas</span>
                        @elseif($order->status === 'shipping')
                            <span class="badge badge-blue">Terkirim ke Kurir</span>
                        @elseif($order->status === 'completed')
                            <span class="badge badge-success">Paket Tiba</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($order->status) }}</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px; color:var(--text-3);">
                        {{ $order->updated_at->diffForHumans() }}
                    </td>
                    <td style="text-align: right;">
                        <button type="button" class="btn-sm" onclick="openUpdateModal('{{ $order->order_number }}', '{{ $order->status }}', '{{ $order->shipping_courier }}', '{{ $order->tracking_number }}')">
                            Update Resi
                        </button>
                        <a href="{{ route('deliveries.label', $order->id) }}" target="_blank" class="btn-sm" style="margin-left:6px; background:var(--surface-2);">
                            Cetak Label
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-3);">Tidak ada riwayat paket untuk diproses.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div style="padding:16px; border-top:1px solid var(--border);">
        {{ $orders->links('vendor.pagination.custom') }}
    </div>
    @endif
</div>

{{-- Update Delivery Modal --}}
<div id="updateModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Status Pengiriman</h3>
            <button type="button" onclick="closeUpdateModal()" style="background:none; border:none; color:var(--text-3); cursor:pointer; font-size:20px;">&times;</button>
        </div>
        <form action="{{ route('deliveries.updateTracking') }}" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="identifier" id="modalIdentifier">
                
                <div class="form-group">
                    <label class="form-label">Ubah Status Menjadi <span style="color:var(--red);">*</span></label>
                    <select name="status" id="modalStatus" class="form-select" required onchange="toggleResiFields()">
                        <option value="processing">Sedang Dikemas (Processing)</option>
                        <option value="shipping">Serahkan ke Kurir (Shipping)</option>
                        <option value="completed">Paket Tiba (Completed)</option>
                    </select>
                </div>
                
                <div id="resiFieldsContainer" style="display:none; padding:12px; background:var(--surface-2); border-radius:8px; border:1px solid var(--border); margin-bottom:16px;">
                    <div class="form-group">
                        <label class="form-label">Penyedia Kurir/Ekspedisi</label>
                        <input type="text" name="shipping_courier" id="modalCourier" class="form-input" placeholder="Contoh: JNE / Sicepat / Kurir Pribadi">
                    </div>
                    
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Nomor Resi Pelacakan</label>
                        <input type="text" name="tracking_number" id="modalTracking" class="form-input" placeholder="Masukkan 12 digit resi asli...">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Catatan Tambahan (Bisa dilihat di history)</label>
                    <textarea name="notes" class="form-textarea" rows="2" placeholder="Cth: Titip di pos satpam pak Budi..."></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-sm" onclick="closeUpdateModal()">Batal</button>
                <button type="submit" class="btn-primary">Update Paket</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdateModal(order_number, current_status, courier, tracking) {
    document.getElementById('modalIdentifier').value = order_number;
    
    // Select status safely
    let statusSelect = document.getElementById('modalStatus');
    for(let i=0; i<statusSelect.options.length; i++) {
        if(statusSelect.options[i].value === current_status) {
            statusSelect.selectedIndex = i;
            break;
        }
    }
    
    document.getElementById('modalCourier').value = courier && courier !== 'null' ? courier : '';
    document.getElementById('modalTracking').value = tracking && tracking !== 'null' ? tracking : '';
    
    toggleResiFields();
    document.getElementById('updateModal').style.display = 'flex';
}

function closeUpdateModal() {
    document.getElementById('updateModal').style.display = 'none';
}

function toggleResiFields() {
    let mode = document.getElementById('modalStatus').value;
    let resiC = document.getElementById('resiFieldsContainer');
    if(mode === 'shipping') {
        resiC.style.display = 'block';
    } else {
        resiC.style.display = 'none';
    }
}
</script>
@endsection
