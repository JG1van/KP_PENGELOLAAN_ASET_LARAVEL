# Sistem Informasi Pengelolaan Aset Sekolah

Aplikasi berbasis web menggunakan Laravel untuk mencatat dan memantau aset sekolah secara digital. Dikembangkan untuk membantu sekolah mengelola data aset secara terstruktur, mulai dari pencatatan hingga pelaporan.

## Fitur Utama

- Manajemen data aset (tambah, edit, hapus)
- Pencatatan riwayat penerimaan, pengecekan, dan penghapusan aset
- Perhitungan penyusutan nilai aset secara otomatis
- Ekspor laporan ke format Excel
- Upload dan penyimpanan dokumen menggunakan Cloudinary
- Generate QR Code untuk setiap aset
- Sistem login multi pengguna (multi-role)

## Tech Stack

- **Backend:** PHP, Laravel
- **Database:** MySQL
- **Cloud Storage:** Cloudinary
- **Lainnya:** QR Code generator, Excel export

## Instalasi

1. Clone atau salin folder project ke direktori `htdocs` (XAMPP) atau direktori web server lainnya.

2. Install dependency:
```bash
   composer install
```

3. Salin file environment:
```bash
   copy .env.example .env
```

4. Konfigurasi file `.env` (koneksi database dan kredensial Cloudinary).

5. Generate application key:
```bash
   php artisan key:generate
```

6. Buat database baru melalui phpMyAdmin sesuai nama pada `.env`.

7. Jalankan migrasi:
```bash
   php artisan migrate
```

8. (Opsional) Jalankan seeder untuk data awal:
```bash
   php artisan db:seed
```

9. Jalankan server lokal:
```bash
   php artisan serve
```

## Status Project

Project ini dikembangkan selama masa Kerja Praktik (KP).
