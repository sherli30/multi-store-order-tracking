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
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
        </div>

        <!-- PENJUALAN -->
        <div class="nav-section">
            <div class="nav-section-label">Penjualan</div>
            <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    <path d="M12 11h4"></path>
                    <path d="M12 16h4"></path>
                    <path d="M8 11h.01"></path>
                    <path d="M8 16h.01"></path>
                </svg>
                Manajemen Pesanan
            </a>
            <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                Riwayat Transaksi
            </a>
        </div>

        <!-- LOGISTIK -->
        <div class="nav-section">
            <div class="nav-section-label">Logistik</div>
            <a href="{{ route('deliveries.index') }}" class="nav-item {{ request()->routeIs('deliveries.index') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                    <line x1="8" y1="2" x2="8" y2="18"></line>
                    <line x1="16" y1="6" x2="16" y2="22"></line>
                </svg>
                Monitoring Pengiriman
            </a>
            <a href="{{ route('deliveries.scan') }}" class="nav-item {{ request()->routeIs('deliveries.scan') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 7V4h3"></path>
                    <path d="M4 17v3h3"></path>
                    <path d="M20 7V4h-3"></path>
                    <path d="M20 17v3h-3"></path>
                    <line x1="12" y1="8" x2="12" y2="16" style="stroke: var(--accent); stroke-width: 2.5px;"></line>
                    <rect x="9" y="8" width="6" height="8"></rect>
                </svg>
                Cek Resi
            </a>
        </div>

        <!-- MASTER LOGISTIK -->
        <div class="nav-section">
            <div class="nav-section-label">Master Logistik</div>
            <a href="{{ route('couriers.index') }}" class="nav-item {{ request()->routeIs('couriers.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                Master Kurir
            </a>
            <a href="{{ route('shipping-services.index') }}" class="nav-item {{ request()->routeIs('shipping-services.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                    <polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"></polygon>
                    <polygon points="12 22.08 12 12 21 6.92 21 17.08 12 22.08"></polygon>
                    <polygon points="12 2 3 6.92 12 12 21 6.92 12 2"></polygon>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                Master Layanan
            </a>
            <div class="nav-item nav-item-has-submenu {{ request()->routeIs('provinces.*') || request()->routeIs('cities.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Master Wilayah
                <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="nav-submenu" style="display: {{ request()->routeIs('provinces.*') || request()->routeIs('cities.*') ? 'flex' : 'none' }};">
                <a href="{{ route('provinces.index') }}" class="nav-submenu-item {{ request()->routeIs('provinces.*') ? 'active' : '' }}">Provinsi</a>
                <a href="{{ route('cities.index') }}" class="nav-submenu-item {{ request()->routeIs('cities.*') ? 'active' : '' }}">Kota / Kabupaten</a>
            </div>
            <a href="{{ route('shipping-rates.index') }}" class="nav-item {{ request()->routeIs('shipping-rates.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                Master Ongkir
            </a>
        </div>

        <!-- KATALOG -->
        <div class="nav-section">
            <div class="nav-section-label">Katalog</div>
            <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                Manajemen Produk
            </a>
            <a href="{{ route('product-categories.index') }}" class="nav-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polygon points="2 17 12 22 22 17"></polygon>
                    <polygon points="2 12 12 17 22 12"></polygon>
                </svg>
                Kategori Produk
            </a>
            <a href="{{ route('stores.index') }}" class="nav-item {{ request()->routeIs('stores.*') ? 'active' : '' }}">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h2.25m-2.25 0v-13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75V21m12 0V15m-12 6V15m12 0a.75.75 0 00-.75-.75H7.5a.75.75 0 00-.75.75V15m12 0h-12" />
                </svg>
                Manajemen Toko
            </a>
        </div>

        <!-- ANALISIS -->
        <div class="nav-section">
            <div class="nav-section-label">Analisis</div>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Laporan Penjualan
            </a>
        </div>

        <!-- LAINNYA -->
        <div class="nav-section">
            <div class="nav-section-label">Lainnya</div>

            {{-- Pelanggan --}}
            <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Pelanggan
            </a>

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
