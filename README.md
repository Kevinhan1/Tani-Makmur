<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

# 🧾 Tani Makmur — Aplikasi Manajemen Data, Transaksi & Laporan

Aplikasi Laravel untuk mengelola transaksi penjualan, pembelian, pembayaran, mutasi rekening, laporan kas, dan piutang.  
Cocok digunakan dalam lingkungan lokal (LAN) dengan banyak pengguna.

---

## 🚀 Fitur Utama

- ✅ Login multi-user (role admin & pengguna)
- 📦 Manajemen barang, pelanggan, pemasok, rekening, dan pengguna
- 💰 Transaksi: pembelian, penjualan, pembayaran, biaya, dan pindah saldo
- 📊 Laporan: mutasi rekening, mutasi stok, laporan penjualan, kas, piutang
- 🧾 Cetak PDF invoice pembelian, penjualan, kas, dan lainnya
- 📁 Ekspor laporan ke format PDF
- 📡 Support akses via LAN (multi-user dengan database bersama)

---

## 🛠️ Requirement

| Komponen   | Versi Minimum |
|------------|----------------|
| PHP        | 8.2+           |
| Composer   | 2.0+           |
| Laravel    | ^12            |
| Database   | MySQL / MariaDB|
| Ekstensi   | DomPDF         |

---

## 📄 Modul Utama

- **Barang** — CRUD barang (dengan harga beli/jual dan status aktif)
- **Penjualan** — Input penjualan, update stok, cetak invoice PDF
- **Pembelian** — Input pembelian, histori pembelian, invoice
- **Pembayaran** — Cicilan/diskon otomatis pada pembelian & penjualan
- **Kas** — Rekap mutasi rekening, saldo awal/akhir, filter jenis transaksi
- **Laporan** — Mutasi rekening, stok, penjualan (bisa diekspor PDF)

---

## 📤 Ekspor PDF

Modul yang mendukung ekspor PDF:

/biaya/pdf
/pindahsaldo/pdf
/kas/pdf
/mutasi-rekening/pdf
/mutasi-stok/pdf
/laporan-penjualan/detail/{notajual}

 Pastikan DomPDF aktif dengan menjalankan:
```bash
composer require barryvdh/laravel-dompdf


📂 Struktur Folder Penting
Folder	Fungsi
app/Http/Controllers	Logika aplikasi: Login, Barang, Kas, dll
resources/views	Tampilan antarmuka (Blade)
routes/web.php	Daftar rute aplikasi
public/	Akses langsung dari browser
database/sql	File database (jika disertakan)


⚙️ Instalasi Lokal (Laragon / XAMPP)
Clone atau Extract Project

Tempatkan di:

C:\laragon\www\tani-makmur (untuk Laragon)

htdocs\tani-makmur (untuk XAMPP)

Install Dependency

bash
Copy
Edit
composer install
Salin & Konfigurasi .env

bash
Copy
Edit
cp .env.example .env
php artisan key:generate
Edit bagian:

makefile
Copy
Edit
DB_DATABASE=tani_makmur
DB_USERNAME=root
DB_PASSWORD=
Import Database
Import file tani_makmur.sql melalui phpMyAdmin atau MySQL CLI.

Jalankan Server Lokal

bash
Copy
Edit
php artisan serve
Akses di browser: http://localhost:8000


🌐 Jalankan di Jaringan LAN (Multi User)
Jalankan server di host:

bash
Copy
Edit
php artisan serve --host=0.0.0.0 --port=8000
Temukan IP lokal (misal 192.168.1.10)

Dari komputer lain, akses:

cpp
Copy
Edit
http://192.168.1.10:8000


🔐 Login Awal (Default)
Nama Pengguna	Kata Kunci
(diatur manual oleh admin utama)	

Kata sandi terenkripsi. Kamu bisa:

Mengganti langsung via database (password_hash()), atau

Tambah user baru lewat menu pengguna.


📬 Kontak Developer
Silakan hubungi pengembang jika terjadi error atau membutuhkan support instalasi lokal & jaringan.