# Sistem Informasi Pengelolaan Aset Sekolah

Aplikasi Laravel untuk mencatat dan memantau aset sekolah secara digital.

## Fitur Utama
- Tambah, edit, hapus aset
- Riwayat penerimaan, pengecekan, penghapusan
- Penurunan nilai aset otomatis
- Ekspor laporan ke Excel
- Upload dokumen ke Cloudinary
- QR Code untuk aset
- Login multi pengguna

## Cara Install
1. Salin folder ke `htdocs`
2. Jalankan:
   - `composer install`
   - `copy .env.example .env`
   - Isi `.env` (DB + Cloudinary)
   - `php artisan key:generate`
   - Buat DB di phpMyAdmin
   - `php artisan migrate`
   - (opsional) `php artisan db:seed`
3. Jalankan:
   - `php artisan serve`

