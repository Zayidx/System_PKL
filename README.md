<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Sistem Prakerin

Sistem manajemen prakerin untuk sekolah menengah kejuruan yang memungkinkan siswa mengajukan dan mengelola prakerin mereka.

## 🚀 **Fitur Utama**

### **1. Fitur Perpanjangan Prakerin**
- ✅ Siswa dapat memperpanjang prakerin dengan perusahaan yang sudah selesai
- ✅ Validasi otomatis untuk memastikan tidak ada prakerin aktif
- ✅ Interface yang user-friendly dengan modal form
- ✅ Data pembimbing dan kepala program otomatis ter-copy

### **2. Real-Time Update Prakerin**
- ✅ **Otomatis:** Prakerin dibuat saat admin perusahaan approve via email
- ✅ **Real-Time:** Dashboard staff hubin ter-update tanpa refresh
- ✅ **Auto-Polling:** Refresh otomatis setiap 10-15 detik
- ✅ **Event-Driven:** Update berdasarkan event Livewire
- ✅ **Error Handling:** Sistem tetap stabil meski ada error

### **3. Sistem Penilaian PKL (BARU)**
- ✅ **Email Otomatis:** Form penilaian dikirim otomatis saat prakerin selesai
- ✅ **Online Form:** Form penilaian web yang user-friendly
- ✅ **Token Security:** Token unik dengan expiry 7 hari
- ✅ **Dashboard Nilai:** Siswa dan staff hubin dapat melihat nilai
- ✅ **Grafik & Statistik:** Visualisasi nilai dengan grafik
- ✅ **Export Feature:** Fitur cetak nilai dan laporan

### **4. Manajemen Pengajuan**
- ✅ Sistem pengajuan prakerin yang terintegrasi
- ✅ Email otomatis untuk approval/rejection
- ✅ Tracking status pengajuan real-time
- ✅ Validasi data otomatis

### **5. Dashboard Monitoring**
- ✅ Dashboard khusus untuk setiap role (Siswa, Staf Hubin, Admin)
- ✅ Statistik real-time
- ✅ Filter dan pencarian data
- ✅ Export data ke PDF

### **6. Manajemen Data Master**
- ✅ CRUD untuk semua entitas (Siswa, Perusahaan, Pembimbing, dll)
- ✅ Validasi data yang robust
- ✅ Upload file dengan preview
- ✅ Soft delete untuk data sensitif

## Cara Menggunakan Fitur Perpanjangan Prakerin

### Untuk Siswa:

1. **Melalui Dashboard:**
   - Login ke dashboard siswa
   - Scroll ke section "Prakerin Selesai - Opsi Perpanjangan"
   - Klik tombol "Perpanjang Prakerin" pada perusahaan yang diinginkan
   - Isi form tanggal mulai dan selesai perpanjangan
   - Klik "Perpanjang Prakerin"

2. **Melalui Riwayat Prakerin:**
   - Buka menu "Riwayat Prakerin"
   - Pilih tab "Riwayat Prakerin"
   - Klik tombol "Perpanjang" pada prakerin yang sudah selesai
   - Isi form dan submit

### Validasi yang Berlaku:
- Hanya prakerin dengan status "selesai" yang dapat diperpanjang
- Tidak boleh ada prakerin aktif yang sedang berlangsung
- Tanggal mulai perpanjangan harus hari ini atau setelahnya
- Tanggal selesai harus setelah tanggal mulai
- Menggunakan pembimbing dan perusahaan yang sama

### Keuntungan Fitur Perpanjangan:
- Memudahkan siswa untuk melanjutkan prakerin di tempat yang sudah familiar
- Menghemat waktu karena tidak perlu mengajukan ulang
- Mempertahankan hubungan dengan pembimbing yang sudah ada
- Proses yang lebih cepat dan efisien

## Teknologi yang Digunakan

- **Backend:** Laravel 11
- **Frontend:** Livewire 3, Bootstrap 5
- **Database:** MySQL
- **Email:** Laravel Mail
- **PDF:** DomPDF

## Instalasi

1. Clone repository
2. Install dependencies: `composer install`
3. Copy `.env.example` ke `.env`
4. Generate key: `php artisan key:generate`
5. Setup database dan jalankan migrasi: `php artisan migrate`
6. Seed data: `php artisan db:seed`
7. Serve aplikasi: `php artisan serve`

## Struktur Database

### Tabel Utama:
- `users` - Akun pengguna
- `siswa` - Data siswa
- `perusahaan` - Data perusahaan
- `pengajuan` - Pengajuan prakerin
- `prakerin` - Data prakerin aktif/selesai
- `pembimbing_sekolah` - Pembimbing dari sekolah
- `pembimbing_perusahaan` - Pembimbing dari perusahaan

### Relasi Kunci:
- Siswa dapat memiliki banyak pengajuan
- Siswa dapat memiliki banyak prakerin (aktif/selesai)
- Prakerin terhubung dengan perusahaan dan pembimbing
- Pengajuan terhubung dengan perusahaan

## Kontribusi

Silakan buat pull request untuk kontribusi. Untuk perubahan besar, buka issue terlebih dahulu untuk mendiskusikan perubahan yang diinginkan.

## Lisensi

[MIT License](LICENSE)
