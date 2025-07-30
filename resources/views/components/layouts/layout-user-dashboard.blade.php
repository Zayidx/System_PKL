{{-- 
    File: resources/views/components/layouts/layout-user-dashboard.blade.php
    Description: Ini adalah file layout utama untuk dashboard pengguna.
                 File ini menyediakan struktur HTML dasar, sidebar, header, footer,
                 dan mengimpor semua aset CSS dan JS yang diperlukan.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'User Dashboard' }}</title>

    {{-- Stylesheet dari Template Mazer --}}
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireStyles
    {{-- Stack untuk menampung style kustom dari view lain --}}
    @stack('styles')
    <style>
        /* Custom style untuk memastikan container #app mengambil tinggi penuh */
        html, body {
            height: 100%;
        }
        #app {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }
        #main {
            /* Memastikan area konten utama dapat tumbuh dan mendorong footer ke bawah */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .page-content {
            /* Membuat area konten yang sebenarnya bisa meluas */
            flex-grow: 1;
        }
        /* Style untuk input di dalam SweetAlert */
        .swal2-input-label {
            text-align: left !important;
            margin: 1em 0 0.5em !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        {{-- Sidebar dari Template Mazer --}}
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="#">Siap Magang</a>
                        </div>
                        <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                            {{-- SVG untuk toggle tema --}}
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21"><g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path><g transform="translate(-210 -1)"><path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path><circle cx="220.5" cy="11.5" r="4"></circle><path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path></g></g></svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="currentColor" d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z"></path></svg>
                        </div>
                        <div class="sidebar-toggler x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    {{-- Ganti dengan path include sidebar Anda yang sebenarnya --}}
                    @include('components.layouts.partials.sidebar-user-dashboard')
                </div>
            </div>
        </div>
        
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <h3>{{ $title ?? 'Halaman' }}</h3>
            </div> 
            <div class="page-content">
                {{-- Di sinilah konten dari view (misal: proses-magang.blade.php) akan dimasukkan --}}
                {{ $slot }}
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2024 &copy; QIA Solution</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span> by <a href="#">Farid</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Script Bawaan Template Mazer --}}
    <script src="{{ asset('assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/compiled/js/app.js') }}"></script>
    
    @livewireScripts
    {{-- Stack untuk menampung script kustom dari view lain --}}
    @stack('scripts')

    <script>
        // Fungsi untuk mendapatkan tema saat ini (terang atau gelap)
        function getThemeMode() {
            return document.documentElement.getAttribute('data-bs-theme') || 'light';
        }

        // Fungsi untuk mendapatkan opsi tema untuk SweetAlert
        function getSwalThemeOptions() {
            const mode = getThemeMode();
            if (mode === 'dark') {
                return {
                    background: '#161B22',
                    color: '#c9d1d9',
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#30363d',
                };
            } else {
                return {
                    background: '#fff',
                    color: '#212529',
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#e0e0e0',
                };
            }
        }
    </script>
    {{-- Script Listener untuk event dari Livewire --}}
    <script>
        document.addEventListener('livewire:init', () => {
            // Listener untuk notifikasi sukses
            Livewire.on('swal:success', event => {
                const theme = getSwalThemeOptions();
                Swal.fire({
                    title: 'Berhasil!',
                    text: event.message,
                    icon: 'success',
                    ...theme, // Menggabungkan opsi tema
                    confirmButtonText: 'Tutup',
                });
            });

            // Listener untuk notifikasi error
            Livewire.on('swal:error', event => {
                const theme = getSwalThemeOptions();
                Swal.fire({
                    title: 'Gagal!',
                    text: event.message,
                    icon: 'error',
                    ...theme,
                    confirmButtonText: 'Tutup',
                });
            });

            // Listener untuk menampilkan form pengajuan magang
            Livewire.on('swal:ajukan', event => {
                const theme = getSwalThemeOptions();
                Swal.fire({
                    title: `Ajukan Magang ke ${event.nama}`,
                    html: `
                        <div class="text-start">
                            <label for="swal-tanggal-mulai" class="swal2-input-label">Tanggal Mulai</label>
                            <input id="swal-tanggal-mulai" type="date" class="form-control" required>
                            
                            <label for="swal-tanggal-selesai" class="swal2-input-label">Tanggal Selesai</label>
                            <input id="swal-tanggal-selesai" type="date" class="form-control" required>

                            <label for="swal-link-cv" class="swal2-input-label">Link CV (Google Drive)</label>
                            <input id="swal-link-cv" type="url" class="form-control" placeholder="https://..." required>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ajukan!',
                    cancelButtonText: 'Batal',
                    ...theme,
                    // Fungsi untuk validasi sebelum modal ditutup
                    preConfirm: () => {
                        const tanggalMulai = document.getElementById('swal-tanggal-mulai').value;
                        const tanggalSelesai = document.getElementById('swal-tanggal-selesai').value;
                        const linkCv = document.getElementById('swal-link-cv').value;

                        if (!tanggalMulai || !tanggalSelesai || !linkCv) {
                            Swal.showValidationMessage(`Harap isi semua field yang diperlukan.`);
                            return false; // Mencegah modal tertutup
                        }
                        
                        if (new Date(tanggalSelesai) <= new Date(tanggalMulai)) {
                            Swal.showValidationMessage('Tanggal selesai harus setelah tanggal mulai.');
                            return false;
                        }

                        // Mengembalikan nilai sebagai objek
                        return { 
                            tanggalMulai: tanggalMulai, 
                            tanggalSelesai: tanggalSelesai, 
                            linkCv: linkCv 
                        };
                    }
                }).then((result) => {
                    // Cek jika user menekan tombol "Ya, ajukan!" dan validasi berhasil
                    if (result.isConfirmed) {
                        // Memanggil metode Livewire dengan membawa data dari form
                        Livewire.dispatch('confirmAjukanMagang', { 
                            id: event.id,
                            formData: result.value // result.value berisi objek dari preConfirm
                        });
                    }
                })
            });

            // Listener untuk dialog konfirmasi (misal: hapus data)
            Livewire.on('swal:confirm', event => {
                const theme = getSwalThemeOptions();
                Swal.fire({
                    title: 'Anda Yakin?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    ...theme,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Memanggil method di komponen Livewire jika dikonfirmasi
                        Livewire.dispatch(event.method, { id: event.id });
                    }
                })
            });
        });
    </script>
</body>
</html>
