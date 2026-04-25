<style>
    .mobile-menu-btn {
        display: none;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-2);
        cursor: pointer;
        margin-right: 12px;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .mobile-menu-btn:hover {
        background: var(--surface);
        color: var(--text-1);
    }

    .mobile-menu-btn svg {
        width: 18px;
        height: 18px;
    }

    /* CSS Tambahan agar dropdown muncul saat aktif */
    .profile-dropdown-wrap.active .profile-menu {
        display: block;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    @media (max-width: 900px) {
        .mobile-menu-btn {
            display: flex;
        }
    }
</style>

<header class="topbar">
    <div class="mobile-menu-btn" onclick="toggleSidebar()" title="Toggle Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </div>

    <div class="topbar-title">
        @if(request()->routeIs('dashboard'))
            Dashboard

            {{-- OPERASIONAL: Pesanan --}}
        @elseif(request()->routeIs('orders.show'))
            Pesanan
            <span>/ Detail Pesanan</span>
        @elseif(request()->routeIs('orders.*'))
            Pesanan
            <span>/ Manajemen Pesanan</span>

            {{-- OPERASIONAL: Transaksi --}}
        @elseif(request()->routeIs('transactions.*'))
            Transaksi
            <span>/ Riwayat Transaksi</span>

            {{-- OPERASIONAL: Pengiriman & Tracking --}}
        @elseif(request()->routeIs('deliveries.scan'))
            Pengiriman & Tracking
            <span>/ Scan Barcode</span>
        @elseif(request()->routeIs('deliveries.history'))
            Pengiriman & Tracking
            <span>/ Riwayat Tracking</span>
        @elseif(request()->routeIs('deliveries.print'))
            Pengiriman & Tracking
            <span>/ Cetak Label</span>
        @elseif(request()->routeIs('deliveries.*'))
            Pengiriman & Tracking
            <span>/ Update Status</span>

            {{-- PRODUK: Produk & Toko --}}
        @elseif(request()->routeIs('products.create'))
            Produk & Toko
            <span>/ Tambah Produk</span>
        @elseif(request()->routeIs('products.edit'))
            Produk & Toko
            <span>/ Edit Produk</span>
        @elseif(request()->routeIs('products.*'))
            Produk & Toko
            <span>/ Manajemen Produk</span>

            {{-- PRODUK: Kategori Produk --}}
        @elseif(request()->routeIs('product-categories.create'))
            Produk & Toko
            <span>/ Tambah Kategori</span>

        @elseif(request()->routeIs('product-categories.edit'))
            Produk & Toko
            <span>/ Edit Kategori</span>

        @elseif(request()->routeIs('product-categories.*'))
            Produk & Toko
            <span>/ Kategori Produk</span>

        @elseif(request()->routeIs('stores.create'))
            Produk & Toko
            <span>/ Tambah Toko</span>
        @elseif(request()->routeIs('stores.edit'))
            Produk & Toko
            <span>/ Edit Toko</span>
        @elseif(request()->routeIs('stores.*'))
            Produk & Toko
            <span>/ Manajemen Toko</span>

            {{-- LAPORAN --}}
        @elseif(request()->routeIs('reports.stores'))
            Laporan
            <span>/ Per Toko</span>
        @elseif(request()->routeIs('reports.consolidated'))
            Laporan
            <span>/ Konsolidasi</span>
        @elseif(request()->routeIs('reports.export'))
            Laporan
            <span>/ Ekspor Data</span>
        @elseif(request()->routeIs('reports.*'))
            Laporan
            <span>/ Laporan</span>

            {{-- DATA: Pelanggan --}}
        @elseif(request()->routeIs('customers.show'))
            Customer
            <span>/ Detail Customer</span>
        @elseif(request()->routeIs('customers.*'))
            Customer
            <span>/ Manajemen Customer</span>

            {{-- PENGATURAN --}}
        @elseif(request()->routeIs('profile.*'))
            Profil Admin

        @else
            {{ request()->route()?->getName() }}
            <span>/ Halaman</span>
        @endif
    </div>

    <div class="topbar-actions">
        <div class="topbar-date" id="dateDisplay">—</div>

        <div class="topbar-btn" title="Notifikasi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <div class="notif-dot"></div>
        </div>

        <div class="topbar-btn" onclick="location.reload()" title="Refresh">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10" />
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
            </svg>
        </div>

        <div class="profile-dropdown-wrap" id="profileDropdown">
            <div class="profile-btn" onclick="toggleProfileDropdown()">
                @if(Auth::user()->avatar)
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" class="profile-avatar object-cover"
                        style="background:none;">
                @else
                    <div class="profile-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                @endif
                <span class="profile-name">{{ Auth::user()->name }}</span>
                <svg class="profile-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </div>

            <div class="profile-menu">
                <div class="profile-menu-header">
                    <div class="profile-menu-name">{{ Auth::user()->name }}</div>
                    <div class="profile-menu-email">{{ Auth::user()->email }}</div>
                    <span class="profile-menu-role">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                </div>
                <div class="profile-menu-body">
                    <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Profil Admin
                    </a>
                    <div class="profile-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="profile-menu-item danger">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // 1. FUNGSI UNTUK MENAMPILKAN TANGGAL (BARU)
    function updateDate() {
        const dateElement = document.getElementById('dateDisplay');
        if (!dateElement) return;

        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        // Format Indonesia: Senin, 1 Januari 2026
        const today = new Date().toLocaleDateString('id-ID', options);
        dateElement.textContent = today;
    }

    // 2. LOGIKA DROPDOWN PROFIL
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('active');
    }

    window.addEventListener('click', function (e) {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });

    // 3. LOGIKA SIDEBAR MOBILE
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) sidebar.classList.toggle('active');
    }

    // 4. JALANKAN FUNGSI SAAT HALAMAN SELESAI DIMUAT
    document.addEventListener('DOMContentLoaded', function () {
        updateDate();
        // Anda juga bisa menambahkan fungsi lain di sini jika diperlukan
    });
</script>
