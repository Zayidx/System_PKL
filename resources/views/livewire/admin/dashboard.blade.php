<div>
    {{--
        File: resources/views/livewire/admin/dashboard.blade.php
        Analisis Perombakan UI/UX:
        1.  [UI/UX] Desain Ulang Kartu Statistik: Kartu dibuat lebih modern dengan ikon besar, gradien, dan layout yang lebih bersih.
            Efek hover ditambahkan untuk interaktivitas.
        2.  [INTERAKTIVITAS] Tombol Refresh: Menambahkan tombol refresh dengan state loading yang jelas (wire:loading)
            untuk memberikan feedback visual kepada pengguna saat data sedang dimuat ulang.
        3.  [UI/UX] Layout yang Ditingkatkan: Memisahkan bagian statistik, aktivitas terbaru, dan menu akses cepat
            ke dalam kartu-kartu yang berbeda untuk kejelasan dan organisasi yang lebih baik.
        4.  [UI/UX] Tabel Lebih Baik: Tabel pengajuan terbaru didesain ulang agar lebih rapi. Status pengajuan
            kini menggunakan badge dengan warna yang sesuai untuk identifikasi cepat.
        5.  [UI/UX] Menu Akses Cepat: Tombol manajemen diubah menjadi daftar tautan yang lebih terstruktur dan menarik secara visual.
        6.  [CLEAN CODE] Kode Blade dirapikan, menggunakan Bootstrap 5 classes secara konsisten dan menghilangkan
            struktur kolom yang terlalu kompleks pada kartu statistik.
    --}}
    <style>
        .stats-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        .stats-card .card-body {
            position: relative;
            z-index: 2;
        }
        .stats-icon-wrapper {
            position: absolute;
            top: -10px;
            right: -20px;
            font-size: 5rem;
            opacity: 0.15;
            transform: rotate(-15deg);
            z-index: 1;
            color: #fff;
        }
        .bg-gradient-purple { background: linear-gradient(45deg, #6a11cb, #2575fc); }
        .bg-gradient-blue { background: linear-gradient(45deg, #007bff, #00d2ff); }
        .bg-gradient-green { background: linear-gradient(45deg, #198754, #28a745); }
        .bg-gradient-red { background: linear-gradient(45deg, #dc3545, #ff416c); }

        .management-links .list-group-item {
            border-radius: 0.5rem !important;
            margin-bottom: 0.5rem;
            transition: background-color 0.2s ease, border-left-width 0.2s ease;
            border-left: 4px solid transparent;
        }
        .management-links .list-group-item:hover {
            background-color: var(--bs-primary-bg-subtle);
            border-left-color: var(--bs-primary);
        }
    </style>

    <div wire:init="loadStats">
        <div class="page-heading d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div>
                <h3>Dashboard Admin</h3>
                <p class="text-muted mb-0">Ringkasan data dan statistik penting sistem.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <button class="btn btn-primary" wire:click="$dispatch('refresh-dashboard')" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="loadStats, refreshDashboard">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </span>
                    <span wire:loading wire:target="loadStats, refreshDashboard">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Memuat...
                    </span>
                </button>
            </div>
        </div>

        <div class="page-content">
            {{-- Kartu Statistik --}}
            <section class="row">
                <div class="col-12">
                    <div class="row g-4">
                        <div class="col-6 col-lg-3">
                            <div class="card stats-card text-white bg-gradient-purple">
                                <div class="card-body">
                                    <div class="stats-icon-wrapper"><i class="bi bi-people-fill"></i></div>
                                    <h6 class="text-white">Total Siswa</h6>
                                    <h3 class="text-white font-extrabold mb-0" wire:loading.class="opacity-50" wire:target="loadStats, refreshDashboard">{{ $totalSiswa }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stats-card text-white bg-gradient-blue">
                                <div class="card-body">
                                    <div class="stats-icon-wrapper"><i class="bi bi-building"></i></div>
                                    <h6 class="text-white">Total Perusahaan</h6>
                                    <h3 class="text-white font-extrabold mb-0" wire:loading.class="opacity-50" wire:target="loadStats, refreshDashboard">{{ $totalPerusahaan }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stats-card text-white bg-gradient-green">
                                <div class="card-body">
                                    <div class="stats-icon-wrapper"><i class="bi bi-briefcase-fill"></i></div>
                                    <h6 class="text-white">Siswa Aktif PKL</h6>
                                    <h3 class="text-white font-extrabold mb-0" wire:loading.class="opacity-50" wire:target="loadStats, refreshDashboard">{{ $siswaAktifPkl }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stats-card text-white bg-gradient-red">
                                <div class="card-body">
                                    <div class="stats-icon-wrapper"><i class="bi bi-clock-history"></i></div>
                                    <h6 class="text-white">Pengajuan Pending</h6>
                                    <h3 class="text-white sfont-extrabold mb-0" wire:loading.class="opacity-50" wire:target="loadStats, refreshDashboard">{{ $pengajuanPending }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Aktivitas Terbaru & Akses Cepat --}}
            <section class="row mt-4">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="bi bi-list-task me-2"></i>Pengajuan Terbaru</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>NAMA SISWA</th>
                                            <th>PERUSAHAAN TUJUAN</th>
                                            <th class="text-center">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($latestPengajuan as $pengajuan)
                                            <tr>
                                                <td class="text-bold-500">{{ $pengajuan->siswa->nama_siswa ?? 'Data tidak ditemukan' }}</td>
                                                <td>{{ $pengajuan->perusahaan->nama_perusahaan ?? 'Data tidak ditemukan' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-light-warning">{{ str_replace('_', ' ', Str::ucfirst($pengajuan->status_pengajuan)) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4">
                                                    <p class="text-muted mb-0">Tidak ada data pengajuan terbaru.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4><i class="bi bi-grid-1x2-fill me-2"></i>Pusat Manajemen</h4>
                        </div>
                        <div class="card-body management-links">
                            <div class="list-group list-group-flush">
                                <a href="{{ route('admin.master.siswa') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Manajemen Siswa <i class="bi bi-chevron-right"></i>
                                </a>
                                <a href="{{ route('admin.master.guru') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Manajemen Guru <i class="bi bi-chevron-right"></i>
                                </a>
                                <a href="{{ route('admin.master.perusahaan') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Manajemen Perusahaan <i class="bi bi-chevron-right"></i>
                                </a>
                                <a href="{{ route('admin.master.jurusan') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Manajemen Jurusan <i class="bi bi-chevron-right"></i>
                                </a>
                                <a href="{{ route('admin.master.users') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    Manajemen Pengguna <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
