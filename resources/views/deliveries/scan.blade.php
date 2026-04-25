@extends('layouts.app')

@section('title', 'Mesin Scanner Barcode')

@section('styles')
.page-header { margin-bottom: 24px; text-align: center; }
.page-header h1 { font-size: 24px; font-weight: 800; color: var(--text-1); }
.page-header p { font-size: 14px; color: var(--text-3); margin-top: 5px; }

.scanner-container {
    max-width: 600px;
    margin: 40px auto;
    background: var(--panel);
    border: 2px dashed var(--border-2);
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.scanner-container:focus-within {
    border-color: var(--accent);
    box-shadow: 0 10px 40px var(--accent-glow);
    transform: translateY(-2px);
}

.scanner-icon {
    width: 64px;
    height: 64px;
    color: var(--accent);
    margin-bottom: 20px;
}

.scanner-status-text {
    font-size: 14px;
    font-weight: 600;
    color: var(--green);
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.scanner-status-text.idle { color: var(--text-3); }

.scanner-input-wrap {
    position: relative;
    max-width: 400px;
    margin: 0 auto;
}

.scanner-input {
    width: 100%;
    font-size: 24px;
    font-family: var(--mono);
    text-align: center;
    padding: 16px;
    border: 2px solid var(--border);
    border-radius: 12px;
    outline: none;
    letter-spacing: 2px;
    color: var(--text-1);
    background: #fff;
    transition: border 0.3s, box-shadow 0.3s;
    box-sizing: border-box;
}

.scanner-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-glow);
}

.scanner-input::placeholder {
    color: var(--border-2);
    font-size: 20px;
    letter-spacing: normal;
}

.status-config-wrap {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    text-align: left;
}

.status-config-wrap label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 8px;
}

.alert {
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 30px;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 12px;
}
.alert-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(16,185,129,0.3); }
.alert-error { background: var(--red-dim); color: var(--red); border: 1px solid rgba(220,38,38,0.3); }

/* Animation wave */
@keyframes pulse {
    0% { transform: scale(0.95); opacity: 0.5; }
    50% { transform: scale(1); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.5; }
}
.scanner-pulse { animation: pulse 2s infinite ease-in-out; }
@endsection

@section('content')
<div class="page-header">
    <h1>Simulator Mesin Scanner Barcode</h1>
    <p>Tembakkan alat Scanner fisik Anda atau ketik Manual (Enter)</p>
</div>

<div class="scanner-container">
    <svg class="scanner-icon scanner-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 7V4h3"></path><path d="M4 17v3h3"></path>
        <path d="M20 7V4h-3"></path><path d="M20 17v3h-3"></path>
        <line x1="12" y1="8" x2="12" y2="16" style="stroke:var(--red); stroke-width:3px;"></line>
        <rect x="9" y="8" width="6" height="8" style="stroke:var(--text-3);"></rect>
    </svg>

    <div class="scanner-status-text" id="statusIndicator">
        <span style="width:10px; height:10px; background:var(--green); border-radius:50%; display:inline-block;"></span>
        Scanner Menunggu (Fokus Aktif)
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('deliveries.updateTracking') }}" method="POST" id="scanForm">
        @csrf
        <div class="scanner-input-wrap">
            <input 
                type="text" 
                name="identifier" 
                class="scanner-input" 
                id="barcodeInput" 
                placeholder="Tembak Barcode Disini" 
                required 
                autofocus 
                autocomplete="off"
            >
        </div>

        <div class="status-config-wrap">
            <label for="nextStatus">Status Sasaran (Auto-Update)</label>
            <select name="status" id="nextStatus" class="scanner-input" style="font-size:14px; text-align:left; letter-spacing:0.5px;">
                <option value="shipping" {{ old('status') === 'shipping' ? 'selected' : '' }}>Paket di Tangan Kurir (Shipping)</option>
                <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Paket Telah Tiba di Penerima (Completed)</option>
                <option value="processing" {{ old('status') === 'processing' ? 'selected' : '' }}>Mulai Proses Packing (Processing)</option>
            </select>
        </div>
    </form>
</div>

<script>
    // Autofocus lock-in mechanism
    const input = document.getElementById('barcodeInput');
    const indicator = document.getElementById('statusIndicator');

    input.addEventListener('blur', () => {
        indicator.innerHTML = '<span style="width:10px; height:10px; background:var(--text-3); border-radius:50%; display:inline-block;"></span> Fokus Hilang (Klik disini)';
        indicator.className = 'scanner-status-text idle';
    });

    input.addEventListener('focus', () => {
        indicator.innerHTML = '<span style="width:10px; height:10px; background:var(--green); border-radius:50%; display:inline-block;"></span> Scanner Menunggu (Fokus Aktif)';
        indicator.className = 'scanner-status-text';
    });

    // Ensure we keep focus as much as possible for pure scanning environment
    document.body.addEventListener('click', (e) => {
        if(e.target.tagName !== 'SELECT' && e.target.tagName !== 'BUTTON') {
            input.focus();
        }
    });

    // Handle form submit automatically happens because physical scanner triggers "Enter" via HID keyboard mapping
</script>
@endsection
