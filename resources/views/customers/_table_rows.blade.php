@foreach($customers as $customer)
<tr class="fade-in-animated">

    {{-- No --}}
    <td class="cell-no"></td>

    {{-- Nama --}}
    <td>
        <div class="avatar-wrap">
            {{--
                Avatar: foto → onerror fallback ke inisial.
                Mirrors customers.show logic exactly.
                $customer->avatar is a plain column on the users table —
                no extra eager-load needed; CustomerController already
                fetches the full User model via User::withCount('orders').
            --}}
            @if($customer->avatar)
                <img
                    src="{{ Storage::url($customer->avatar) }}"
                    alt="{{ $customer->name }}"
                    class="avatar"
                    style="object-fit:cover;"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="avatar" style="display:none;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
            @else
                <div class="avatar">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <div class="customer-name">{{ $customer->name }}</div>
            </div>
        </div>
    </td>

    {{-- Kontak --}}
    <td>
        <div class="contact-email">{{ $customer->email }}</div>
        <div class="contact-phone">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.38 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            {{ $customer->phone ?: 'Belum diisi' }}
        </div>
    </td>

    {{-- Total Pesanan --}}
    <td>
        <div class="order-count">{{ number_format($customer->orders_count) }}</div>
        <div class="order-label">Transaksi</div>
    </td>

    {{-- Status --}}
    <td>
        @if($customer->is_active)
            <span class="badge badge-active">Aktif</span>
        @else
            <span class="badge badge-inactive">Diblokir</span>
        @endif
    </td>

    {{-- Terdaftar --}}
    <td>
        <div class="reg-date">{{ $customer->created_at->format('d M Y') }}</div>
        <div class="reg-relative">{{ $customer->created_at->diffForHumans() }}</div>
    </td>

    {{-- Aksi --}}
    <td>
        <div class="actions-cell">
            <a href="{{ route('customers.show', $customer->id) }}" class="btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                Detail
            </a>
        </div>
    </td>

</tr>
@endforeach
