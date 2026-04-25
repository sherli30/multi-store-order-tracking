# Multi-Store Order Tracking

Aplikasi berbasis web untuk manajemen produk, pesanan, dan transaksi untuk banyak toko (multi-store).

## Persyaratan Sistem

Sebelum memulai, pastikan sistem Anda sudah terinstal:
- **PHP** (Versi >= 8.1 direkomendasikan)
- **Composer** (Manajer dependensi PHP)
- **Node.js & NPM** (Manajer dependensi JavaScript)
- **MySQL / MariaDB** (Untuk database)
- **Git** (Untuk version control)

---

## 🚀 Tata Cara Instalasi (Git Clone Sampai Berhasil)

Ikuti langkah-langkah di bawah ini secara berurutan agar aplikasi dapat berjalan dengan baik di komputer lokal Anda.

### 1. Clone Repository
Buka terminal (Command Prompt, PowerShell, atau Git Bash), arahkan ke folder direktori web Anda (misal `c:\laragon\www` atau `htdocs`), lalu jalankan perintah berikut:
```bash
git clone <url-repository-github-anda> multi-store-order-tracking
cd multi-store-order-tracking
```
*(Ganti `<url-repository-github-anda>` dengan URL repo git aplikasi ini, atau abaikan url jika sudah berada dalam folder proyek).*

### 2. Install Dependensi PHP (Composer)
Unduh semua pustaka (library) PHP yang dibutuhkan oleh framework Laravel dengan menjalankan:
```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)
Unduh dan kompilasi semua aset frontend (seperti Tailwind CSS, Alpine.js, atau Bootstrap/JS lainnya):
```bash
npm install
npm run build
```
*(Catatan: Anda juga bisa menggunakan `npm run dev` di tab terminal terpisah jika ingin mengaktifkan hot-reload saat sedang mendevelop frontend).*

### 4. Konfigurasi Environment (`.env`)
Laravel membutuhkan file konfigurasi lingkungan (environment variables). Salin file contoh yang sudah disediakan:
- **Di Windows / Command Prompt:**
  ```cmd
  copy .env.example .env
  ```
- **Di Linux / Mac / Git Bash:**
  ```bash
  cp .env.example .env
  ```

### 5. Konfigurasi Database
1. Buka database manager favorit Anda (misal: phpMyAdmin, HeidiSQL, DBeaver, atau Laragon Database).
2. **Buat database baru** yang masih kosong. Contoh beri nama database: `multi_store` atau `multi_store_order_tracking`.
3. Buka file `.env` di teks editor (VS Code) dan ubah bagian konfigurasi database agar sesuai dengan database lokal Anda. Contoh bawaan Laragon/XAMPP:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=multi_store_order_tracking  # Sesuaikan dengan nama database yang baru Anda buat
   DB_USERNAME=root
   DB_PASSWORD=                            # Kosongkan jika root tidak memakai password
   ```

### 6. Generate Application Key
Buat kunci enkripsi keamanan aplikasi Laravel dengan perintah:
```bash
php artisan key:generate
```

### 7. Migrasi Database dan Seeder (Membuat Tabel & Data Awal)
Langkah ini sangat penting untuk membangun kerangka tabel di dalam database beserta mengisi data awal (seperti data Toko dan Kategori Produk) yang dibutuhkan agar aplikasi bisa berfungsi.
Jalankan perintah berikut:
```bash
php artisan migrate --seed
```
*Perintah `--seed` akan secara otomatis menjalankan `DatabaseSeeder`, yang akan mengeksekusi `StoreSeeder` dan `ProductCategorySeeder`.*

### 8. Tautkan Storage (Storage Link)
Aplikasi ini memiliki fitur upload foto (misal logo toko, gambar produk). Agar gambar tersebut bisa ditampilkan di browser, Anda wajib menjalankan perintah ini untuk membuat symlink folder `storage/app/public` ke folder `public/storage`:
```bash
php artisan storage:link
```

### 9. Jalankan Aplikasi
Setelah semua pengaturan selesai, jalankan server pengembangan lokal Laravel:
```bash
php artisan serve
```
Aplikasi sekarang bisa diakses melalui browser di alamat:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🛠️ Ringkasan Perintah (Cheat Sheet)
Jika Anda membuka ulang proyek ini suatu saat dan hanya ingin memperbarui dependensi serta database, Anda cukup menjalankan:
```bash
composer install
npm install && npm run build
php artisan migrate
php artisan serve
```

## Fitur Utama Sistem
- **Manajemen Multi-Toko**: Kelola banyak toko sekaligus.
- **Kategori & Produk**: Data kategori dan produk bersifat dinamis sesuai toko yang dipilih (menggunakan pemfilteran relasional).
- **Manajemen Stok**: Atur dan tambah/kurangi persediaan barang.
- **Pelacakan Transaksi**: Kelola pesanan/transaksi pembeli di masing-masing toko.
