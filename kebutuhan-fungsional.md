# DOKUMEN KEBUTUHAN FUNGSIONAL
**Sistem Manajemen Pemesanan, Penjualan & Tracking Pengiriman (Multi-Vendor Marketplace)**
**Proyek Tugas Akhir (TA)**

## 1. PENDAHULUAN
Dokumen ini mendeskripsikan kebutuhan fungsional sistem marketplace multi-toko (Multi-Vendor Marketplace) yang terintegrasi dengan pembayaran otomatis Midtrans, *multi-store checkout*, dan manajemen logistik. Kebutuhan fungsional dikategorikan berdasarkan peran (*role*) pengguna yang terlibat, yaitu: **Administrator Web**, **Administrator Mobile**, dan **Customer Mobile**. Sistem dirancang untuk mendukung operasional e-commerce berskala multi-vendor dengan keamanan transaksi dan pengelolaan inventori terpadu.

---

## 2. KEBUTUHAN FUNGSIONAL: ADMINISTRATOR WEB
Akses melalui aplikasi berbasis web (*Web App*) untuk mengelola seluruh operasional marketplace, mulai dari produk, transaksi, logistik, hingga laporan penjualan.

| ID | Fitur Utama | Deskripsi |
| :--- | :--- | :--- |
| FR-001 | Dashboard | Menampilkan ringkasan metrik (KPI) operasional secara real-time seperti total pesanan, pendapatan, pelanggan aktif, tren penjualan, dan notifikasi pesanan yang memerlukan perhatian. |
| FR-002 | Manajemen Pesanan | Melihat, memfilter, dan mengelola pesanan dari seluruh toko. Meliputi konfirmasi pesanan masuk, pembaruan status pesanan (Diproses, Dikirim, Selesai), dan pembatalan pesanan beserta alasannya. |
| FR-003 | Manajemen Transaksi (Midtrans) | Memantau status sinkronisasi pembayaran otomatis dari webhook Midtrans (*Paid, Pending, Failed/Expired*), menangani pengembalian dana (*refund*), dan pencatatan transaksi rekonsiliasi manual. |
| FR-004 | Manajemen Produk & Katalog | Mengelola katalog produk dari setiap toko, mencakup operasi CRUD (*Create, Read, Update, Delete*) pada data toko, kategori produk, spesifikasi harga, berat (kg), serta pengelolaan stok yang didukung *Pessimistic Locking* guna menghindari bentrokan stok (*race conditions*). |
| FR-005 | Manajemen Logistik | Mengelola data pendukung logistik, seperti matriks tarif ongkos kirim (ongkir) per wilayah (Provinsi/Kota), master layanan (Reguler, Cargo), dan daftar vendor kurir yang tersedia. |
| FR-006 | Tracking & Pemindaian Resi | Memantau siklus hidup pengiriman secara terpusat. Admin dapat memindai (scan) barcode resi menggunakan *webcam* atau scanner USB, serta memperbarui riwayat pelacakan (*tracking*) paket secara manual. |
| FR-007 | Cetak Label Pengiriman | Menghasilkan dan mencetak label *Airway Bill* (AWB) pesanan dalam format PDF siap cetak (mendukung printer thermal) yang dilengkapi barcode resi, alamat tujuan, dan rincian produk. |
| FR-008 | Laporan Penjualan (Reports) | Menghasilkan laporan analitik penjualan baik secara per-toko maupun laporan konsolidasi seluruh toko, dengan fitur ekspor data ke dalam format dokumen PDF dan file Excel. |
| FR-009 | Manajemen Pelanggan | Memantau daftar pelanggan yang terdaftar, melihat riwayat transaksi masing-masing pelanggan, dan mengontrol akses keamanan akun (seperti melakukan *suspend* atau pemblokiran). |
| FR-010 | Profil & Autentikasi | Melakukan *login* aman menggunakan kredensial administrator, memperbarui detail profil dan kata sandi, serta melakukan *logout* dari panel admin. |

---

## 3. KEBUTUHAN FUNGSIONAL: ADMINISTRATOR MOBILE
Akses melalui aplikasi mobile (*Mobile App*) bagi staf operasional lapangan atau kurir internal untuk mencatat pergerakan barang secara *real-time*.

| ID | Fitur Utama | Deskripsi |
| :--- | :--- | :--- |
| FR-011 | Autentikasi Admin | Melakukan *login* ke aplikasi mobile secara khusus dengan menggunakan kredensial Administrator yang tersinkronisasi. |
| FR-012 | Scan Barcode Native | Memindai resi fisik pengiriman paket secara langsung menggunakan kamera *native* pada perangkat *smartphone* petugas di lapangan. |
| FR-013 | Update Status Logistik | Memperbarui titik pelacakan (*checkpoint*) paket (contoh: "Paket dihubungkan", "Paket Tiba") secara *real-time* ke server pangkalan data berdasarkan hasil pemindaian resi. |
| FR-014 | Riwayat Tracking Logistik | Melihat rekam jejak historis pemindaian resi serta memeriksa kembali kesesuaian data pesanan secara komprehensif saat di lapangan. |

---

## 4. KEBUTUHAN FUNGSIONAL: CUSTOMER MOBILE
Akses melalui aplikasi mobile (*Mobile App*) yang didedikasikan bagi pelanggan (*Customer*) untuk berbelanja pada marketplace.

| ID | Fitur Utama | Deskripsi |
| :--- | :--- | :--- |
| FR-015 | Registrasi & Autentikasi | Mendaftarkan akun pelanggan baru, melakukan *login* dengan kredensial email, mengelola *session* (via JWT token), serta memulihkan akses secara aman apabila terjadi *unauthorized* akses. |
| FR-016 | Lupa Kata Sandi (Forgot Password) | Melakukan proses reset dan pembuatan kata sandi baru yang diverifikasi melalui pengiriman *link* ke *email* pelanggan (terintegrasi dengan *Laravel Password Broker*). |
| FR-017 | Profil & Alamat | Memperbarui informasi profil pengguna dan menetapkan titik alamat pengiriman *default* (Provinsi dan Kota) guna mengotomatiskan kalkulasi logistik. |
| FR-018 | Katalog, Toko & Pencarian | Menjelajahi antarmuka *Multi-Vendor* untuk melihat daftar toko yang tersedia, menjelajah produk per kategori, serta melakukan pencarian produk berdasarkan kata kunci (deskripsi produk, spesifikasi, atau ketersediaan stok). |
| FR-019 | Keranjang Belanja (Cart) | Menambahkan produk yang diminati ke dalam keranjang, menyesuaikan kuantitas (jumlah) barang, dan meninjau subtotal kalkulasi harga sesaat sebelum *checkout*. |
| FR-020 | Daftar Keinginan (Wishlist) | Menyimpan produk pilihan pelanggan ke dalam *Wishlist* personal untuk memfasilitasi pembelian atau transaksi di waktu yang akan datang. |
| FR-021 | Multi-Store Checkout | Menyelesaikan proses *checkout* multi-toko (pembelian dari berbagai vendor dalam satu transaksi). Sistem secara otomatis memecah faktur per toko dan mengkalkulasikan ongkos kirim lintas rute berdasarkan berat paket. |
| FR-022 | Pembayaran Otomatis (Midtrans) | Memproses transaksi pembayaran yang disinkronisasi melalui *gateway* Midtrans *Snap* secara mandiri, mendukung metode pembayaran QRIS, dompet digital (*E-Wallet*), *Virtual Account*, dan *Credit Card*. |
| FR-023 | Riwayat & Pelacakan Pesanan | Memeriksa seluruh daftar riwayat transaksi (*Pesanan Saya*) secara rinci yang difilter ke dalam status tahap pemrosesan (Belum Bayar, Diproses, Dikirim, Selesai, atau Dibatalkan). |
| FR-024 | Scan Barcode Pelanggan | Memindai resi fisik produk (menggunakan kamera smartphone *Customer*) saat paket tiba, untuk memantau detail validitas resi dan mencocokkannya dengan *timeline* pesanan di server. |
| FR-025 | Konfirmasi Penerimaan | Menyelesaikan pesanan (*Completed*) secara sadar (konfirmasi penerimaan) oleh pelanggan melalui sistem apabila pesanan telah benar-benar diterima dalam kondisi sesuai. |
| FR-026 | Komunikasi Penjual (WhatsApp) | Melakukan interaksi komunikasi langsung dengan perwakilan toko atau administrator via *WhatsApp API* melalui tombol penghubung yang tersemat pada laman detail toko. |
| FR-027 | Notifikasi Sistem (Push Notif) | Menerima peringatan pemberitahuan (*push notifications*) penting, meliputi konfirmasi pembuatan pesanan, keberhasilan *webhook* pembayaran, hingga pergerakan resi pengiriman. |
