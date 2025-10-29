# Fitur Perpanjangan Prakerin

## Deskripsi
Fitur ini memungkinkan siswa untuk memperpanjang prakerin di perusahaan yang sudah diselesaikan sebelumnya. Fitur ini memudahkan siswa untuk melanjutkan pengalaman prakerin mereka tanpa perlu mengajukan ulang ke perusahaan baru.

## Lokasi Fitur
1. **Dashboard Siswa** - Section "Prakerin Selesai - Opsi Perpanjangan"
2. **Halaman Riwayat Prakerin** - Tab "Riwayat Prakerin"

## Cara Menggunakan

### 1. Melalui Dashboard Siswa
1. Login ke akun siswa
2. Scroll ke bagian "Prakerin Selesai - Opsi Perpanjangan"
3. Klik tombol "Perpanjang Prakerin" pada perusahaan yang diinginkan
4. Isi form perpanjangan:
   - Tanggal mulai perpanjangan
   - Tanggal selesai perpanjangan
   - Keterangan (opsional)
5. Klik "Perpanjang Prakerin"

### 2. Melalui Riwayat Prakerin
1. Buka menu "Riwayat Prakerin"
2. Pilih tab "Riwayat Prakerin"
3. Cari prakerin dengan status "Selesai"
4. Klik tombol "Perpanjang"
5. Isi form dan submit

## Validasi yang Berlaku

### Validasi Prakerin:
- ✅ Hanya prakerin dengan status "selesai" yang dapat diperpanjang
- ✅ Tidak boleh ada prakerin aktif yang sedang berlangsung
- ✅ Prakerin harus milik siswa yang sedang login

### Validasi Tanggal:
- ✅ Tanggal mulai perpanjangan harus hari ini atau setelahnya
- ✅ Tanggal selesai harus setelah tanggal mulai
- ✅ Tanggal tidak boleh kosong

### Validasi Form:
- ✅ Keterangan maksimal 500 karakter (opsional)
- ✅ Semua field wajib diisi kecuali keterangan

## Alur Proses Perpanjangan

1. **Pemilihan Prakerin**
   - Siswa memilih prakerin yang sudah selesai
   - Sistem memvalidasi status prakerin

2. **Validasi Kondisi**
   - Cek apakah ada prakerin aktif
   - Cek kepemilikan prakerin
   - Cek status prakerin

3. **Input Data Perpanjangan**
   - Tanggal mulai dan selesai
   - Keterangan (opsional)

4. **Pembuatan Prakerin Baru**
   - Menggunakan data dari prakerin lama:
     - NIS siswa
     - ID perusahaan
     - NIP pembimbing sekolah
     - ID pembimbing perusahaan
     - NIP kepala program
   - Data baru:
     - Tanggal mulai dan selesai perpanjangan
     - Status: "aktif"
     - Keterangan perpanjangan

5. **Notifikasi**
   - Sukses: "Prakerin berhasil diperpanjang!"
   - Error: Pesan error sesuai kondisi

## Keuntungan Fitur

### Untuk Siswa:
- ✅ Menghemat waktu karena tidak perlu mengajukan ulang
- ✅ Mempertahankan hubungan dengan pembimbing yang sudah ada
- ✅ Menggunakan perusahaan yang sudah familiar
- ✅ Proses yang lebih cepat dan efisien

### Untuk Sekolah:
- ✅ Memudahkan tracking prakerin siswa
- ✅ Mengurangi beban administrasi
- ✅ Mempertahankan hubungan dengan perusahaan mitra

### Untuk Perusahaan:
- ✅ Mendapatkan siswa yang sudah familiar dengan lingkungan kerja
- ✅ Mengurangi waktu training
- ✅ Mempertahankan hubungan dengan sekolah

## Teknis Implementasi

### Komponen yang Dimodifikasi:
1. **app/Livewire/User/RiwayatPrakerin.php**
   - Menambahkan properti modal perpanjangan
   - Method `bukaModalPerpanjangan()`
   - Method `tutupModalPerpanjangan()`
   - Method `prosesPerpanjangan()`
   - Listener untuk event dari dashboard

2. **app/Livewire/User/Dashboard.php**
   - Menambahkan properti modal perpanjangan
   - Method untuk menangani modal perpanjangan
   - Section untuk menampilkan prakerin selesai

3. **resources/views/livewire/pengguna/riwayat-prakerin.blade.php**
   - Menambahkan modal perpanjangan
   - Menambahkan tombol perpanjangan di tabel
   - Form input tanggal dan keterangan

4. **resources/views/livewire/pengguna/dashboard.blade.php**
   - Menambahkan section prakerin selesai
   - Menambahkan modal perpanjangan
   - Tombol perpanjangan di dashboard

### Database:
- Menggunakan tabel `prakerin` yang sudah ada
- Tidak memerlukan perubahan struktur database
- Prakerin perpanjangan disimpan sebagai record baru

### Keamanan:
- ✅ Validasi kepemilikan prakerin
- ✅ Validasi status prakerin
- ✅ Validasi input tanggal
- ✅ Pengecekan prakerin aktif
- ✅ CSRF protection melalui Livewire

## Troubleshooting

### Masalah Umum:

1. **Modal tidak muncul**
   - Pastikan tidak ada JavaScript error
   - Cek console browser untuk error
   - Pastikan Bootstrap JS sudah dimuat

2. **Tombol perpanjangan tidak berfungsi**
   - Pastikan prakerin status "selesai"
   - Cek apakah ada prakerin aktif
   - Pastikan siswa adalah pemilik prakerin

3. **Error validasi tanggal**
   - Pastikan tanggal mulai hari ini atau setelahnya
   - Pastikan tanggal selesai setelah tanggal mulai
   - Format tanggal harus YYYY-MM-DD

4. **Prakerin tidak terupdate**
   - Refresh halaman setelah perpanjangan
   - Cek apakah ada error di log Laravel
   - Pastikan database connection normal

### Debug:
- Cek log Laravel: `storage/logs/laravel.log`
- Cek console browser untuk JavaScript error
- Gunakan `dd()` untuk debug data di method

## Pengembangan Selanjutnya

### Fitur yang Bisa Ditambahkan:
1. **Notifikasi Email** - Kirim email ke pembimbing saat perpanjangan
2. **Approval Admin** - Perpanjangan perlu approval admin
3. **Riwayat Perpanjangan** - Track berapa kali diperpanjang
4. **Limit Perpanjangan** - Maksimal berapa kali bisa diperpanjang
5. **Perubahan Pembimbing** - Opsi untuk ganti pembimbing saat perpanjangan

### Optimasi:
1. **Caching** - Cache data prakerin selesai
2. **Pagination** - Pagination untuk daftar prakerin selesai
3. **Search** - Pencarian prakerin selesai
4. **Export** - Export data perpanjangan

## Testing

### Test Cases:
1. ✅ Perpanjangan prakerin selesai
2. ✅ Validasi prakerin aktif
3. ✅ Validasi tanggal
4. ✅ Validasi kepemilikan
5. ✅ Error handling
6. ✅ UI/UX testing

### Manual Testing:
1. Login sebagai siswa
2. Cari prakerin dengan status selesai
3. Klik tombol perpanjangan
4. Isi form dengan data valid
5. Submit dan cek hasil
6. Test dengan data invalid
7. Test dengan prakerin aktif

## Dokumentasi API

### Method yang Ditambahkan:

#### `bukaModalPerpanjangan($idPrakerin)`
- **Parameter:** `$idPrakerin` (int) - ID prakerin yang akan diperpanjang
- **Return:** void
- **Fungsi:** Membuka modal perpanjangan dan memvalidasi prakerin

#### `tutupModalPerpanjangan()`
- **Parameter:** tidak ada
- **Return:** void
- **Fungsi:** Menutup modal dan reset form

#### `prosesPerpanjangan()`
- **Parameter:** tidak ada
- **Return:** void
- **Fungsi:** Memproses perpanjangan prakerin

### Event yang Ditambahkan:
- `bukaModalPerpanjangan` - Event untuk membuka modal dari dashboard

## Kontribusi

Untuk menambahkan fitur atau memperbaiki bug:
1. Fork repository
2. Buat branch baru
3. Implementasi perubahan
4. Test thoroughly
5. Submit pull request

## Support

Jika ada pertanyaan atau masalah dengan fitur ini, silakan:
1. Cek dokumentasi ini
2. Cek troubleshooting section
3. Buat issue di repository
4. Hubungi tim development 