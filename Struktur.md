# Struktur Komponen Livewire

Dokumen ini merangkum susunan folder, berkas, dan tanggung jawab utamanya setelah penataan ulang komponen Livewire.

## `app/Livewire`

- `BerandaUtama.php` – Komponen landing page publik yang menampilkan statistik ringkas dan informasi perusahaan mitra.
- `Autentikasi/`
  - `Masuk.php` – Formulir login dan logika otentikasi multi-role.
  - `Daftar.php` – Pendaftaran akun siswa lengkap dengan OTP email.
  - `Keluar.php` – Tombol keluar yang menutup sesi dan mengarahkan kembali ke halaman masuk.
  - `LupaSandi.php` – Proses pemulihan kata sandi berbasis OTP.
- `Administrator/`
  - `DasborUtama.php` – Ringkasan statistik utama untuk superadmin.
  - `DasborPengelolaanPengguna.php` – CRUD akun pengguna dan manajemen peran.
  - `DasborPerusahaan.php` – Pengelolaan data perusahaan mitra.
  - `DasborSiswa.php` – Data induk siswa beserta pencarian dan paginasi.
  - `DasborKelas.php` – Administrasi kelas beserta relasi jurusan.
  - `DasborJurusan.php` – Pengelolaan jurusan dan kompetensi terkait.
  - `DasborGuru.php` – Manajemen data guru pembimbing sekolah.
  - `DasborWaliKelas.php` – CRUD wali kelas dan akun pengguna terkait.
  - `DasborPembimbingPerusahaan.php` – Pengelolaan pembimbing dari sisi perusahaan.
  - `DasborPembimbingSekolah.php` – Pengelolaan pembimbing sekolah.
  - `DasborStafHubin.php` – Administrasi staf hubin (hubungan industri).
  - `DasborKepalaSekolah.php` – Data akun kepala sekolah.
  - `DasborKepalaProgram.php` – Data akun kepala program keahlian.
  - `DasborKompetensiNilai.php` – Manajemen rubrik/kompetensi penilaian PKL.
  - `DaftarPenilaianPkl.php` – Daftar dan ringkasan nilai PKL per siswa.
  - `DetailNilaiPkl.php` – Detail nilai PKL, termasuk statistik per kompetensi.
  - `DasborPengajuan.php` – Monitoring pengajuan prakerin per kelas.
  - `DasborPengajuanSiswa.php` – Pengajuan prakerin per siswa di dalam kelas.
  - `DasborStatusPengajuanSiswa.php` – Jejak status pengajuan untuk satu siswa.
- `StafHubin/`
  - `DasborUtama.php` – Ringkasan aktivitas prakerin dan pengajuan untuk staf hubin.
  - `DasborPrakerin.php` – Daftar kelas beserta progres prakerin.
  - `DasborPrakerinSiswa.php` – Detail prakerin per siswa dalam satu kelas.
  - `DasborStatusPrakerinSiswa.php` – Status lengkap prakerin satu siswa, termasuk aksi lanjutan.
  - `DasborNilaiSiswa.php` – Rekap nilai PKL per kelas.
  - `MitraPerusahaan.php` – Pengelolaan status kerja sama perusahaan.
- `Pengguna/`
  - `Dasbor.php` – Dasbor siswa: status pengajuan, progres prakerin, dan ringkasan nilai.
  - `Pengajuan.php` – Daftar lowongan dan pengajuan prakerin siswa.
  - `AjukanPerusahaanBaru.php` – Formulir usulan perusahaan baru oleh siswa.
  - `ProsesPengajuan.php` – Alur detail pengajuan prakerin siswa.
  - `ProsesMagang.php` – Pemantauan aktivitas prakerin aktif.
  - `RiwayatPrakerin.php` – Riwayat, perpanjangan, dan evaluasi prakerin siswa.
  - `NilaiSiswa.php` – Ringkasan nilai PKL yang diterima siswa.

## `resources/views/livewire`

- `beranda-utama.blade.php` – Tampilan landing page publik.
- `autentikasi/`
  - `masuk.blade.php`, `daftar.blade.php`, `lupa-sandi.blade.php`, `keluar.blade.php` – Tampilan antarmuka autentikasi.
- `administrator/` – Satu berkas per komponen di `app/Livewire/Administrator` (mis. `dasbor-utama.blade.php`, `dasbor-kompetensi-nilai.blade.php`) sebagai pasangan tampilan setiap dasbor/halaman admin.
- `staf-hubin/` – Tampilan khusus staf hubin (`dasbor-utama.blade.php`, `dasbor-prakerin.blade.php`, dll).
- `pengguna/` – Tampilan sisi siswa (`dasbor.blade.php`, `pengajuan.blade.php`, `riwayat-prakerin.blade.php`, dll).

> Setiap komponen Livewire memiliki pasangan tampilan dengan nama kebab-case yang identik, memudahkan pemetaan antara kelas dan blade.

## `resources/views/components/layouts`

- `layout-admin-dashboard.blade.php` – Layout dasar area administrator.
- `layout-staf-hubin-dashboard.blade.php` – Layout area staf hubin.
- `layout-user-dashboard.blade.php` – Layout area siswa/pengguna.
- `partials/sidebar-*.blade.php` – Komponen sidebar untuk tiap peran (administrator, staf hubin, pengguna) yang kini mengarah ke rute baru.

Dokumen ini dapat diperbarui saat modul baru ditambahkan agar struktur tetap terjaga dan mudah dipahami.
