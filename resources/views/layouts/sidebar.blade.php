<!-- ═════════════════════════ SIDEBAR ═════════════════════════ -->
<aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="logo-icon">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: auto;">
        </div>
        <span class="brand-name">Toko<span>Pakan</span></span>
    </a>

    <style>
        .nav-submenu {
            padding-left: 28px;
            margin-top: 4px;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-submenu-item {
            display: block;
            padding: 6px 12px;
            text-decoration: none;
            color: var(--text-3);
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .nav-submenu-item:hover {
            color: var(--text-1);
            background: var(--surface);
        }

        .nav-submenu-item.active {
            color: var(--accent);
            background: var(--accent-dim);
            font-weight: 600;
        }

        .chevron {
            margin-left: auto;
            width: 14px;
            height: 14px;
            transition: transform 0.2s;
            opacity: 0.5;
        }

        .nav-item.active .chevron {
            opacity: 1;
        }

        .nav-item-has-submenu {
            cursor: pointer;
        }
    </style>

    <nav style="padding-bottom: 80px;">
        <!-- UTAMA -->
        <div class="nav-section">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
        </div>

        <!-- OPERASIONAL -->
        <div class="nav-section">
            <div class="nav-section-label">Operasional</div>

            {{-- Pesanan (Simplified to a flat link with badge) --}}
            <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                    <path
                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                    </path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                Pesanan
            </a>

            {{-- Transaksi (Simplified to a flat link) --}}
            <a href="{{ route('transactions.index') }}"
                class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                Transaksi
            </a>

            {{-- Pengiriman & Tracking (Accordion kept) --}}
            @php $isDeliveries = request()->routeIs('deliveries.*'); @endphp
            <a href="#" class="nav-item nav-item-has-submenu {{ $isDeliveries ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                Pengiriman & Tracking
                <svg class="chevron" style="transform: {{ $isDeliveries ? 'rotate(180deg)' : 'rotate(0deg)' }};"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </a>
            <div class="nav-submenu" style="display: {{ $isDeliveries ? 'flex' : 'none' }};">
                <a href="{{ route('deliveries.index') }}"
                    class="nav-submenu-item {{ request()->routeIs('deliveries.index') ? 'active' : '' }}">Update
                    Status</a>
                <a href="{{ route('deliveries.scan') }}"
                    class="nav-submenu-item {{ request()->routeIs('deliveries.scan') ? 'active' : '' }}">Scan
                    Barcode</a>
                <a href="#" class="nav-submenu-item">Cetak Label</a>
                <a href="{{ route('deliveries.history') }}"
                    class="nav-submenu-item {{ request()->routeIs('deliveries.history') ? 'active' : '' }}">Riwayat
                    Tracking</a>
            </div>
        </div>

        <!-- PRODUK -->
        <div class="nav-section">
            <div class="nav-section-label">Produk</div>

            @php
                $isProducts = request()->routeIs('products.*')
                    || request()->routeIs('stores.*')
                    || request()->routeIs('product-categories.*');
            @endphp
            <a href="#" class="nav-item nav-item-has-submenu {{ $isProducts ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                Produk & Toko
                <svg class="chevron" style="transform: {{ $isProducts ? 'rotate(180deg)' : 'rotate(0deg)' }};"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </a>
            <div class="nav-submenu" style="display: {{ $isProducts ? 'flex' : 'none' }};">
                <a href="{{ route('products.index') }}"
                    class="nav-submenu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">Manajemen Produk</a>
                <a href="{{ route('product-categories.index') }}"
                    class="nav-submenu-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                    Kategori Produk
                </a>
                <a href="{{ route('stores.index') }}"
                    class="nav-submenu-item {{ request()->routeIs('stores.*') ? 'active' : '' }}">Manajemen Toko</a>
            </div>
        </div>

        <!-- LAPORAN -->
        <div class="nav-section">
            <div class="nav-section-label">Laporan</div>
            @php $isReports = request()->routeIs('reports.*'); @endphp
            <a href="#" class="nav-item nav-item-has-submenu {{ $isReports ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Laporan
                <svg class="chevron" style="transform: {{ $isReports ? 'rotate(180deg)' : 'rotate(0deg)' }};"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </a>
            <div class="nav-submenu" style="display: {{ $isReports ? 'flex' : 'none' }};">
                <a href="{{ route('reports.stores') }}"
                    class="nav-submenu-item {{ request()->routeIs('reports.stores') ? 'active' : '' }}">Laporan Per
                    Toko</a>
                <a href="{{ route('reports.consolidated') }}"
                    class="nav-submenu-item {{ request()->routeIs('reports.consolidated') ? 'active' : '' }}">Laporan
                    Konsolidasi</a>
                <a href="{{ route('reports.export') }}"
                    class="nav-submenu-item {{ request()->routeIs('reports.export') ? 'active' : '' }}">Ekspor Data</a>
            </div>
        </div>

        <!-- DATA -->
        <div class="nav-section">
            <div class="nav-section-label">Data</div>
            <a href="{{ route('customers.index') }}"
                class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Customer
            </a>
        </div>

        <!-- PENGATURAN -->
        <div class="nav-section">
            <div class="nav-section-label">Pengaturan</div>
            @php $isSettings = request()->routeIs('profile.*'); @endphp
            <a href="#" class="nav-item nav-item-has-submenu {{ $isSettings ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                    </path>
                </svg>
                Pengaturan
                <svg class="chevron" style="transform: {{ $isSettings ? 'rotate(180deg)' : 'rotate(0deg)' }};"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </a>
            <div class="nav-submenu" style="display: {{ $isSettings ? 'flex' : 'none' }};">
                <a href="#" class="nav-submenu-item">Pengaturan Pengiriman</a>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const submenuToggles = document.querySelectorAll('.nav-item-has-submenu');
            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();

                    const submenu = this.nextElementSibling;
                    const chevron = this.querySelector('.chevron');

                    if (submenu.style.display === 'flex') {
                        submenu.style.display = 'none';
                        chevron.style.transform = 'rotate(0deg)';
                    } else {
                        submenu.style.display = 'flex';
                        chevron.style.transform = 'rotate(180deg)';
                    }
                });
            });
        });
    </script>
</aside>
