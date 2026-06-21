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

            {{-- PENJUALAN --}}
        @elseif(request()->routeIs('orders.show'))
            Penjualan
            <span>/ Detail Pesanan</span>
        @elseif(request()->routeIs('orders.*'))
            Penjualan
            <span>/ Kelola Pesanan</span>
        @elseif(request()->routeIs('transactions.*'))
            Penjualan
            <span>/ Riwayat Transaksi</span>

            {{-- LOGISTIK --}}
        @elseif(request()->routeIs('deliveries.scan'))
            Logistik
            <span>/ Cek Resi</span>
        @elseif(request()->routeIs('deliveries.history'))
            Logistik
            <span>/ Riwayat Tracking</span>
        @elseif(request()->routeIs('deliveries.print'))
            Logistik
            <span>/ Cetak Label</span>
        @elseif(request()->routeIs('deliveries.*'))
            Logistik
            <span>/ Monitoring Pengiriman</span>

            {{-- DATA LOGISTIK --}}
        @elseif(request()->routeIs('couriers.*'))
            Data Logistik
            <span>/ Kelola Kurir</span>
        @elseif(request()->routeIs('shipping-services.*'))
            Data Logistik
            <span>/ Kelola Layanan</span>
        @elseif(request()->routeIs('provinces.*'))
            Data Logistik
            <span>/ Kelola Provinsi</span>
        @elseif(request()->routeIs('cities.*'))
            Data Logistik
            <span>/ Kelola Kota & Kabupaten</span>
        @elseif(request()->routeIs('shipping-rates.*'))
            Data Logistik
            <span>/ Kelola Ongkir</span>

            {{-- KATALOG --}}
        @elseif(request()->routeIs('products.create'))
            Katalog
            <span>/ Tambah Produk</span>
        @elseif(request()->routeIs('products.edit'))
            Katalog
            <span>/ Edit Produk</span>
        @elseif(request()->routeIs('products.*'))
            Katalog
            <span>/ Kelola Produk</span>

        @elseif(request()->routeIs('product-categories.create'))
            Katalog
            <span>/ Tambah Kategori</span>
        @elseif(request()->routeIs('product-categories.edit'))
            Katalog
            <span>/ Edit Kategori</span>
        @elseif(request()->routeIs('product-categories.*'))
            Katalog
            <span>/ Kategori Produk</span>

        @elseif(request()->routeIs('stores.create'))
            Katalog
            <span>/ Tambah Toko</span>
        @elseif(request()->routeIs('stores.edit'))
            Katalog
            <span>/ Edit Toko</span>
        @elseif(request()->routeIs('stores.*'))
            Katalog
            <span>/ Kelola Toko</span>

            {{-- ANALISIS --}}
        @elseif(request()->routeIs('reports.*'))
            Analisis
            <span>/ Laporan Penjualan</span>

            {{-- LAINNYA --}}
        @elseif(request()->routeIs('customers.show'))
            Lainnya
            <span>/ Detail Customer</span>
        @elseif(request()->routeIs('customers.*'))
            Lainnya
            <span>/ Kelola Customer</span>
        @elseif(request()->routeIs('profile.*'))
            Lainnya
            <span>/ Profil Administrator</span>

        @else
            Sistem
            <span>/ {{ str_replace('.', ' ', request()->route()?->getName()) }}</span>
        @endif
    </div>

    <div class="topbar-actions">
        <div class="topbar-date" id="dateDisplay">—</div>

        <div class="profile-dropdown-wrap" id="notifDropdown">
            @php
                $unreadCount = auth()->user()->unreadNotifications->count();
            @endphp
            <div class="topbar-btn" title="Notifikasi" onclick="toggleNotifDropdown()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                @if($unreadCount > 0)
                    <div class="notif-dot"></div>
                @endif
            </div>

            <div class="profile-menu" style="width: 320px;">
                <div class="profile-menu-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="profile-menu-name">Notifikasi</div>
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.markAllAsRead') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="background:none; border:none; color:var(--accent); font-size:11px; font-weight:700; cursor:pointer;">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    @endif
                </div>
                <div class="profile-menu-body" style="max-height: 300px; overflow-y: auto; padding: 0;">
                    @forelse(auth()->user()->unreadNotifications->take(5) as $notif)
                        <a href="{{ route('notifications.redirect', $notif->id) }}" style="padding: 12px 16px; border-bottom: 1px solid var(--border); display: flex; gap: 10px; text-decoration: none; color: inherit; transition: background 0.15s;" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                            <div style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 8px; background: var(--accent-dim); color: var(--accent); display: flex; align-items: center; justify-content: center;">
                                @if(isset($notif->data['type']) && $notif->data['type'] == 'new_order')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                @elseif(isset($notif->data['type']) && $notif->data['type'] == 'payment')
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                                @elseif(isset($notif->data['type']) && in_array($notif->data['type'], ['cancel', 'return_requested', 'return_approved', 'return_rejected']))
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                @elseif(isset($notif->data['type']) && in_array($notif->data['type'], ['shipping', 'delivered']))
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                @else
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 12.5px; font-weight: 700; color: var(--text-1);">{{ $notif->data['title'] ?? 'Notifikasi Sistem' }}</div>
                                <div style="font-size: 11.5px; color: var(--text-2); margin-top: 2px;">{{ $notif->data['message'] ?? '' }}</div>
                                <div style="font-size: 10px; color: var(--text-4); margin-top: 4px;">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; padding-left: 8px;">
                                <span style="display: block; width: 8px; height: 8px; background: var(--accent); border-radius: 50%;" title="Baru"></span>
                            </div>
                        </a>
                    @empty
                        <div style="padding: 30px 20px; text-align: center; color: var(--text-3); font-size: 12.5px;">
                            Tidak ada notifikasi baru.
                        </div>
                    @endforelse
                </div>
                <div style="padding: 10px; border-top: 1px solid var(--border); text-align: center;">
                    <a href="{{ route('notifications.index') }}" style="font-size: 12px; font-weight: 700; color: var(--accent); text-decoration: none;">Lihat Semua Notifikasi</a>
                </div>
            </div>
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
                {{--
                    Avatar Header — disamakan dengan avatar Edit Profil:
                    wrapper bundar (.profile-avatar-wrap-sm) + <img> object-fit:cover
                    (.profile-avatar-img-sm), atau placeholder inisial bergradasi
                    (.profile-avatar-placeholder-sm) bila user belum punya foto.
                    Tidak ada lagi class ganda "object-cover" yang tidak terdefinisi.
                --}}
                <div class="profile-avatar-wrap-sm">
                    @if(Auth::user()->avatar)
                        <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar"
                            class="profile-avatar-img-sm">
                    @else
                        <div class="profile-avatar-placeholder-sm">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    @endif
                </div>
                <span class="profile-name">{{ Auth::user()->name }}</span>
                <svg class="profile-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </div>

            <div class="profile-menu">
                <div class="profile-menu-header">
                    <div class="profile-menu-name">{{ Auth::user()->name }}</div>
                    <div class="profile-menu-email">{{ Auth::user()->email }}</div>
                    <span class="profile-menu-role">{{ Auth::user()->role === 'administrator' ? 'Administrator' : (Auth::user()->role === 'logistics' ? 'Tim Logistik' : ucfirst(str_replace('_', ' ', Auth::user()->role))) }}</span>
                </div>
                <div class="profile-menu-body">
                    <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        Profil Administrator
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

    // 2. LOGIKA DROPDOWN PROFIL & NOTIFIKASI
    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('active');
        document.getElementById('notifDropdown').classList.remove('active');
    }

    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        dropdown.classList.toggle('active');
        document.getElementById('profileDropdown').classList.remove('active');
    }

    window.addEventListener('click', function (e) {
        const profileDropdown = document.getElementById('profileDropdown');
        const notifDropdown = document.getElementById('notifDropdown');

        if (profileDropdown && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('active');
        }
        if (notifDropdown && !notifDropdown.contains(e.target)) {
            notifDropdown.classList.remove('active');
        }
    });

    // 3. LOGIKA SIDEBAR MOBILE
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
            if (overlay) overlay.classList.toggle('open');
        }
    }

    // 4. JALANKAN FUNGSI SAAT HALAMAN SELESAI DIMUAT
    document.addEventListener('DOMContentLoaded', function () {
        updateDate();
        // Anda juga bisa menambahkan fungsi lain di sini jika diperlukan
    });
</script>
