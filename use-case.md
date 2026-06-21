# DOKUMEN USE CASE SISTEM
## Sistem Manajemen Pemesanan, Penjualan & Tracking Pengiriman

---

### 1. DAFTAR AKTOR
| Aktor | Jenis | Deskripsi |
| :--- | :--- | :--- |
| **Customer** | Manusia | Pengguna aplikasi mobile (Android/iOS) yang melakukan pencarian produk, pemesanan, pembayaran (Midtrans), dan pelacakan pesanan. |
| **Administrator** | Manusia | Pengelola sistem melalui Web Admin (Laravel) dan Mobile Admin (Flutter) yang menangani konfirmasi pesanan, logistik, pengiriman, master data, manajemen katalog, laporan, dan kontrol akses customer. |
| **Midtrans** | Sistem Eksternal | Payment gateway pihak ketiga yang mengamankan transaksi pembayaran dan mengirim webhook sinkronisasi status (Settlement, Expired, Cancel, Deny). |

---

### 2. MATRIKS AKTOR & USE CASE

| ID | Nama Use Case | Customer | Administrator | Midtrans |
| :--- | :--- | :---: | :---: | :---: |
| UC-01 | Autentikasi Akun | ✅ | ✅ | |
| UC-02 | Eksplorasi Katalog & Toko Dinamis | ✅ | | |
| UC-03 | Pemesanan & Hitung Ongkir (Checkout) | ✅ | | |
| UC-04 | Pembayaran Pesanan (Midtrans Snap) | ✅ | | ✅ |
| UC-05 | Sinkronisasi Status Pembayaran (Webhook) | | | ✅ |
| UC-06 | Pelacakan Pesanan & Konfirmasi Penerimaan | ✅ | | |
| UC-07 | Scan Barcode / Cek Resi Pengiriman | ✅ | ✅ | |
| UC-08 | Manajemen Penjualan (Pesanan & Transaksi) | | ✅ | |
| UC-09 | Monitoring Logistik & Cetak Label | | ✅ | |
| UC-10 | Manajemen Master Logistik (Wilayah & Ongkir) | | ✅ | |
| UC-11 | Manajemen Katalog (Toko, Kategori, Produk) | | ✅ | |
| UC-12 | Laporan Penjualan & Ekspor Data | | ✅ | |
| UC-13 | Kontrol Akses & Manajemen Customer | | ✅ | |

---

### 3. RELASI DIAGRAM (INCLUDE & EXTEND)
- **UC-04 Pembayaran Pesanan** `<<include>>` **UC-03 Pemesanan (Checkout)**
- **UC-05 Sinkronisasi Status Pembayaran** `<<extend>>` **UC-08 Manajemen Penjualan** *(Otomatis mengubah status order menjadi "Perlu Diproses")*
- **UC-08 Manajemen Penjualan** `<<include>>` **Restorasi Stok (Pessimistic Locking)** *(Pada saat pembatalan order)*
- **UC-03 Pemesanan (Checkout)** `<<include>>` **Deduksi Stok Otomatis**
- **UC-09 Monitoring Logistik** `<<extend>>` **UC-07 Scan Barcode / Cek Resi Pengiriman**

---

### 4. DETAIL USE CASE (USE CASE SPECIFICATIONS)

#### UC-01: Autentikasi Akun
- **Aktor:** Customer, Administrator
- **Deskripsi:** Proses autentikasi ke dalam sistem menggunakan email dan kata sandi. Customer dapat melakukan registrasi mandiri via Mobile App.
- **Preconditions:** Aktor memiliki akses ke aplikasi (Web/Mobile).
- **Trigger:** Aktor membuka aplikasi tanpa sesi yang aktif.
- **Main Flow:**
  1. Aktor memasukkan kredensial (email & password) pada halaman Login.
  2. Sistem memvalidasi kredensial.
  3. Sistem mengembalikan token autentikasi (untuk REST API Mobile) atau sesi (Web).
  4. Aktor diarahkan ke Home Page (Customer) atau Dashboard (Administrator).
- **Alternative Flow (Registrasi Customer):**
  1. Customer memilih opsi Registrasi.
  2. Customer memasukkan nama lengkap, email, nomor telepon, dan password.
  3. Sistem memvalidasi ketersediaan email dan membuat entitas Customer.
- **Exception Flow:**
  - Jika kredensial tidak valid, sistem mengembalikan respon Unauthorized (401).
  - Jika akun Customer berstatus *suspended* (diblokir oleh Administrator), sistem menolak login dan menampilkan alert keamanan.
- **Postconditions:** Aktor memiliki hak akses aktif sesuai perannya.

#### UC-02: Eksplorasi Katalog & Toko Dinamis
- **Aktor:** Customer
- **Deskripsi:** Penelusuran produk yang difilter dan dipisahkan secara ketat berdasarkan toko penjual.
- **Preconditions:** Customer berada di aplikasi mobile (Tab Home/Katalog).
- **Main Flow:**
  1. Customer memilih Toko yang diinginkan dari daftar toko dinamis.
  2. Sistem mengambil data kategori spesifik dan katalog produk khusus toko tersebut via API.
  3. Customer menelusuri produk dan melihat detail (deskripsi, spesifikasi, varian berat, dan stok real-time).
- **Postconditions:** Customer dapat mengambil keputusan untuk berbelanja produk.

#### UC-03: Pemesanan & Hitung Ongkir (Checkout)
- **Aktor:** Customer
- **Deskripsi:** Proses finalisasi keranjang belanja, penentuan lokasi, dan kalkulasi biaya logistik.
- **Preconditions:** Customer telah login dan memiliki produk di keranjang.
- **Trigger:** Customer menekan tombol "Checkout".
- **Main Flow:**
  1. Customer meninjau produk dan kuantitas.
  2. Sistem mengunci baris produk di database secara pesimistik (`lockForUpdate()`) untuk mencegah race condition (stok ganda).
  3. Customer memilih Provinsi dan Kota tujuan pengiriman.
  4. Sistem melakukan agregasi berat total (kg) pesanan dan mencocokkan dengan Master Ongkir.
  5. Sistem menampilkan subtotal produk, biaya pengiriman, dan Grand Total.
  6. Customer mengkonfirmasi pembuatan pesanan.
  7. Sistem mengurangi stok produk (*Deducted*) dan merekam pesanan.
- **Exception Flow:**
  - Jika saat checkout stok tidak mencukupi (diambil pengguna lain sepersekian detik sebelumnya), sistem menggagalkan transaksi dan menampilkan peringatan stok habis.
- **Postconditions:** Order terbentuk dengan status "Pending" (Menunggu Pembayaran).

#### UC-04: Pembayaran Pesanan (Midtrans Snap)
- **Aktor:** Customer, Midtrans
- **Deskripsi:** Automasi pembayaran menggunakan payment gateway.
- **Preconditions:** Order berstatus "Pending".
- **Main Flow:**
  1. Sistem meng-generate request transaksi ke Midtrans untuk mendapatkan *Snap Token*.
  2. Customer memilih "Bayar Sekarang" di Mobile App.
  3. Tampilan checkout Midtrans (Snap UI) muncul.
  4. Customer memilih metode (Virtual Account, QRIS, e-Wallet).
  5. Customer menyelesaikan pembayaran.
- **Postconditions:** Order menunggu sinkronisasi dari Webhook.

#### UC-05: Sinkronisasi Status Pembayaran (Webhook)
- **Aktor:** Midtrans
- **Deskripsi:** Komunikasi asinkron dari Midtrans ke backend Laravel untuk memperbarui status transaksi.
- **Trigger:** Midtrans mengirimkan HTTP POST.
- **Main Flow (Lunas):**
  1. Webhook berisi `order_id` dan `transaction_status` (settlement/capture) diterima sistem.
  2. Sistem memvalidasi Signature Key Midtrans untuk otentikasi.
  3. Sistem mengupdate status tabel Transaksi menjadi "Paid".
  4. Sistem memajukan status Order menjadi "Perlu Diproses".
  5. Firebase mengirim Push Notification ke Customer (`type: payment`).
- **Alternative Flow (Expired / Cancel / Deny):**
  1. Webhook menginformasikan status kegagalan.
  2. Sistem membatalkan Order (Cancelled).
  3. Sistem memicu *Restorasi Stok*, mengembalikan kuantitas yang sebelumnya dideduksi secara otomatis ke inventori toko.
- **Postconditions:** Sinkronisasi berhasil, status konsisten, dan integritas stok terjaga.

#### UC-06: Pelacakan Pesanan & Konfirmasi Penerimaan
- **Aktor:** Customer
- **Deskripsi:** Memantau siklus hidup pesanan secara mandiri.
- **Main Flow:**
  1. Customer membuka tab "Pesanan Saya".
  2. Sistem mengambil data Order beserta relasi `TrackingHistory`.
  3. Customer melihat timeline pergerakan logistik (misal: "Barang diserahkan ke JNE").
  4. Setelah paket fisik diterima, Customer menekan tombol "Selesai".
  5. Sistem mengubah status pesanan menjadi "Completed".

#### UC-07: Scan Barcode / Cek Resi Pengiriman
- **Aktor:** Administrator, Customer
- **Deskripsi:** Fungsi pemindaian barcode resi/label pengiriman lintas platform.
- **Preconditions:** Aplikasi terhubung dengan kamera device (Mobile) atau peripheral scanner (Web).
- **Main Flow (Flutter Mobile - Administrator / Customer):**
  1. Aktor membuka tab Scan Barcode.
  2. Aktor mengarahkan kamera native ke barcode label.
  3. API menerima nomor resi dan mereturn detail Tracking.
  4. (Khusus Administrator Mobile): Dapat melakukan penambahan titik tracking baru langsung dari lapangan.
- **Alternative Flow (Web Administrator):**
  1. Administrator membuka halaman Cek Resi.
  2. Menggunakan barcode scanner USB, resi terinput otomatis dan halaman menampilkan histori.

#### UC-08: Manajemen Penjualan (Pesanan & Transaksi)
- **Aktor:** Administrator
- **Deskripsi:** Kontrol terpusat untuk siklus penjualan lintas-toko.
- **Main Flow:**
  1. Administrator membuka menu Manajemen Pesanan di Web Admin.
  2. Memantau pesanan yang berstatus "Perlu Diproses".
  3. Administrator memproses pesanan dan mengupdate status menjadi "Processing".
  4. Memantau kelengkapan pembayaran di menu Riwayat Transaksi.
- **Alternative Flow (Refund/Pembatalan Manual):**
  1. Administrator membatalkan order karena kendala operasional.
  2. Sistem memulihkan stok otomatis.
  3. Administrator mencatat proses pengembalian dana (Refund).

#### UC-09: Monitoring Logistik & Cetak Label
- **Aktor:** Administrator
- **Deskripsi:** Penanganan serah-terima logistik dan pencetakan Airway Bill (AWB).
- **Main Flow:**
  1. Administrator membuka menu Monitoring Pengiriman.
  2. Menginput nomor resi valid dan menyimpannya.
  3. Status order berubah menjadi "Shipping", notifikasi `shipping` terkirim ke ponsel Customer.
  4. Administrator menekan aksi "Cetak Label".
  5. Sistem men-generate file PDF berstandar thermal, mencakup Barcode Resi, detail penerima, nama pengirim (Toko), dan flag Tagih COD jika relevan.

#### UC-10: Manajemen Master Logistik (Wilayah & Ongkir)
- **Aktor:** Administrator
- **Deskripsi:** Pengelolaan basis data geografis dan tarif ekspedisi dinamis.
- **Main Flow:**
  1. Administrator membuka kelompok menu Master Logistik.
  2. Mengelola data Master Provinsi dan Master Kota.
  3. Menambah profil Master Kurir dan Master Layanan (contoh: JNE Cargo, internal, dll).
  4. Mendefinisikan matriks Master Ongkir (Kota Asal -> Kota Tujuan = Rp/Kg).
- **Postconditions:** Perubahan tarif akan segera dihitung secara real-time pada checkout Customer.

#### UC-11: Manajemen Katalog (Toko, Kategori, Produk)
- **Aktor:** Administrator
- **Deskripsi:** Operasi CRUD (Create, Read, Update, Delete) entitas dagang dengan aturan relasi hirarkis.
- **Main Flow:**
  1. Administrator mengelola data Toko (membuat/nonaktifkan).
  2. Administrator membuat Kategori yang secara sistematis dikunci pada satu Toko.
  3. Administrator menambah Produk (foto, deskripsi, harga, varian berat kilogram).
  4. Administrator memanipulasi stok produk melalui modul `StockMovement` logs untuk akuntabilitas (mencegah manipulasi stok mentah).
- **Postconditions:** Pembaruan produk langsung tercermin di katalog Flutter Customer.

#### UC-12: Laporan Penjualan & Ekspor Data
- **Aktor:** Administrator
- **Deskripsi:** Modul intelijen bisnis dan ekspor akuntansi.
- **Main Flow:**
  1. Administrator mengakses menu Laporan Penjualan.
  2. Melakukan filter rentang tanggal (Datepicker) dan memfilter spesifik berdasarkan Toko.
  3. Sistem melakukan agregasi pendapatan, transaksi sukses, dan produk terlaris.
  4. Administrator menekan "Ekspor ke PDF" atau "Ekspor ke Excel".
  5. File berhasil diunduh.

#### UC-13: Kontrol Akses & Manajemen Customer
- **Aktor:** Administrator
- **Deskripsi:** Audit, pemantauan, dan kebijakan keamanan tingkat pengguna.
- **Main Flow:**
  1. Administrator meninjau tabel Customer.
  2. Mengklik detail untuk melihat total riwayat belanja spesifik pengguna.
  3. Jika terdeteksi anomali/fraud, Administrator melakukan toggle "Blokir/Nonaktifkan Akun".
  4. Akun bersangkutan secara instan kehilangan akses pada REST API (HTTP 401).
