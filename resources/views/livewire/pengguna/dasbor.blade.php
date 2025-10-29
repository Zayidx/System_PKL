{{-- Dasbor siswa yang merangkum statistik pengajuan, magang aktif, dan aksi perpanjangan. --}}
{{-- 
    File: resources/views/livewire/pengguna/dasbor.blade.php
    Deskripsi: Rekonstruksi total UI/UX dashboard siswa dengan fokus pada kejelasan, modernitas, dan pengalaman pengguna yang superior.
    Perubahan Kunci:
    - Palet Warna Baru: Menggunakan skema warna yang lebih lembut dan profesional untuk kenyamanan visual.
    - Tipografi yang Ditingkatkan: Ukuran dan bobot font diatur untuk hierarki dan keterbacaan yang lebih baik.
    - Desain Kartu Modern: Kartu informasi didesain ulang dengan bayangan halus, padding yang lebih baik, dan tata letak konten yang rapi.
    - Tata Letak Responsif yang Disempurnakan: Memastikan tampilan tetap optimal di berbagai ukuran layar.
    - Peningkatan Visual: Elemen seperti lencana (badge), progress bar, dan ikon disesuaikan dengan tema baru.
--}}
@push('styles')
<style>
    /* === Dashboard Redesign Styles V2 === */
    :root {
        --dashboard-bg: #f7f8fc;
        --dashboard-card-bg: #ffffff;
        --dashboard-card-border: #eef2f7;
        --dashboard-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --dashboard-text-primary: #2c3e50;
        --dashboard-text-secondary: #8a99b5;
        --dashboard-text-muted: #abb9d0;
        --dashboard-primary: #3671ff;
        --dashboard-primary-soft: rgba(54, 113, 255, 0.1);
        --dashboard-success: #10b981;
        --dashboard-success-soft: rgba(16, 185, 129, 0.1);
        --dashboard-warning: #f59e0b;
        --dashboard-warning-soft: rgba(245, 158, 11, 0.1);
        --dashboard-danger: #ef4444;
        --dashboard-danger-soft: rgba(239, 68, 68, 0.1);
        --dashboard-info: #3b82f6;
        --dashboard-info-soft: rgba(59, 130, 246, 0.1);
        --dashboard-border-radius: 0.75rem; /* 12px */
    }

    [data-bs-theme="dark"] {
        --dashboard-bg: #121828;
        --dashboard-card-bg: #1a2234;
        --dashboard-card-border: #2c3a54;
        --dashboard-text-primary: #e0e5f3;
        --dashboard-text-secondary: #8a99b5;
        --dashboard-text-muted: #6b7a99;
    }

    .page-content {
        background-color: var(--dashboard-bg);
        color: var(--dashboard-text-secondary);
    }

    /* === Main Layout & Header === */
    .dashboard-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.75rem;
    }
    @media (min-width: 992px) {
        .dashboard-layout {
            grid-template-columns: 340px 1fr;
        }
    }

    .dashboard-header {
        margin-bottom: 2rem;
    }
    .dashboard-header h2 {
        font-weight: 700;
        color: var(--dashboard-text-primary);
        font-size: 1.75rem;
    }
    .dashboard-header p {
        color: var(--dashboard-text-secondary);
        font-size: 1rem;
    }
    .dashboard-header .btn-primary {
        background-color: var(--dashboard-primary);
        border-color: var(--dashboard-primary);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: var(--dashboard-border-radius);
    }

    /* === Base Card Style === */
    .dashboard-card {
        background-color: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-card-border);
        border-radius: var(--dashboard-border-radius);
        box-shadow: var(--dashboard-shadow);
        height: 100%;
        transition: all 0.3s ease-in-out;
    }
    .dashboard-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--dashboard-card-border);
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dashboard-text-primary);
    }
    .dashboard-card-body {
        padding: 1.5rem;
    }

    /* === Left Column: Profile & Nav === */
    .profile-card .profile-avatar {
        width: 80px; height: 80px; object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--dashboard-card-bg);
        box-shadow: 0 0 0 2px var(--dashboard-primary);
    }
    .profile-card .avatar-placeholder {
        width: 80px; height: 80px;
        border-radius: 50%;
        font-size: 2.25rem;
        font-weight: 600;
        background-color: var(--dashboard-primary-soft);
        color: var(--dashboard-primary);
    }
    .profile-card h5 {
        color: var(--dashboard-text-primary);
        font-weight: 700;
    }
    .profile-info-list { list-style: none; padding: 0; margin: 1.5rem 0 0 0; }
    .profile-info-list li {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.85rem 0;
        border-top: 1px solid var(--dashboard-card-border);
        font-size: 0.9rem;
    }
    .profile-info-list li:first-child { border-top: none; }
    .profile-info-list .icon { font-size: 1.2rem; color: var(--dashboard-primary); width: 20px; text-align: center; }
    .profile-info-list .value { font-weight: 500; color: var(--dashboard-text-primary); }

    .quick-nav .nav-link {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.85rem 1.5rem;
        color: var(--dashboard-text-secondary);
        font-weight: 500;
        border-radius: 0.5rem;
        transition: background-color 0.2s, color 0.2s;
        font-size: 0.95rem;
    }
    .quick-nav .nav-link:hover, .quick-nav .nav-link.active {
        background-color: var(--dashboard-primary-soft);
        color: var(--dashboard-primary);
    }
    .quick-nav .nav-link .icon { font-size: 1.25rem; }

    /* === Right Column: Main Content === */
    .hero-status-card {
        border-radius: var(--dashboard-border-radius);
        padding: 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        border: none;
        height: 70%;
    }
    .hero-status-card::before {
        content: '';
        position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        background-color: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: scale(1);
        transition: transform 0.5s ease;
    }
    .hero-status-card:hover::before {
        transform: scale(1.2);
    }
    .hero-status-card h5 { font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem; opacity: 0.8; }
    .hero-status-card h3 { font-weight: 700; font-size: 2rem; margin-top: 0.5rem; }
    .hero-status-card.bg-success { background: linear-gradient(45deg, #10b981, #0f9b6c); }
    .hero-status-card.bg-primary { background: linear-gradient(45deg, #3b82f6, #2563eb); }
    .hero-status-card.bg-secondary { background: linear-gradient(45deg, #8AB5F0FF, #527DB9FF); }

    .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (min-width: 576px) { .stat-grid { grid-template-columns: repeat(4, 1fr); } }
    .stat-card {
        text-align: center;
        padding: 1.5rem 1rem;
        background-color: var(--dashboard-card-bg);
        border: 1px solid var(--dashboard-card-border);
        border-radius: var(--dashboard-border-radius);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--dashboard-shadow);
    }
    .stat-card .stat-number { font-size: 2.25rem; font-weight: 700; color: var(--dashboard-primary); }
    .stat-card .stat-label { font-size: 0.9rem; color: var(--dashboard-text-secondary); margin-top: 0.25rem; }

    .activity-list .list-group-item {
        background-color: var(--dashboard-card-bg);
        border-color: var(--dashboard-card-border);
        padding: 1.25rem 1.5rem;
        transition: background-color 0.2s;
    }
    .activity-list .list-group-item[onclick] { cursor: pointer; }
    .activity-list .list-group-item[onclick]:hover { background-color: var(--dashboard-bg); }
    .activity-list .list-group-item h6 { color: var(--dashboard-text-primary); font-weight: 600; }
    .activity-list .badge { font-size: 0.75rem; font-weight: 600; padding: 0.4em 0.8em; }
    .badge.bg-warning { background-color: var(--dashboard-warning-soft) !important; color: var(--dashboard-warning) !important; }
    .badge.bg-info { background-color: var(--dashboard-info-soft) !important; color: var(--dashboard-info) !important; }
    .badge.bg-success { background-color: var(--dashboard-success-soft) !important; color: var(--dashboard-success) !important; }
    .badge.bg-danger { background-color: var(--dashboard-danger-soft) !important; color: var(--dashboard-danger) !important; }
    
    .progress { height: 1rem; border-radius: 1rem; background-color: var(--dashboard-card-border); }
    .progress-bar { font-size: 0.7rem; font-weight: 600; border-radius: 1rem; }
    .progress-bar.bg-success { background-color: var(--dashboard-success); }
    
    .info-list .list-group-item {
        background-color: transparent;
        padding: 1rem 0;
        border-color: var(--dashboard-card-border);
    }
    .info-list .list-group-item span { color: var(--dashboard-text-secondary); }
    .info-list .list-group-item strong { color: var(--dashboard-text-primary); text-align: right; }
    .info-list .list-group-item i { color: var(--dashboard-primary); }

    .alert-info {
        background-color: var(--dashboard-info-soft);
        color: var(--dashboard-info);
        border-color: rgba(59, 130, 246, 0.3);
    }

</style>
@endpush

@if($siswa)
<div>
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>Selamat Datang, {{ strtok($siswa->nama_siswa, ' ') }}!</h2>
            <p>Ini adalah ringkasan aktivitas dan progres magang Anda.</p>
        </div>
        
    </div>

    <!-- Main Layout Grid -->
    <div class="dashboard-layout">
        <!-- Left Column -->
        <div class="d-flex flex-column gap-4">
            <!-- Profile Card -->
            <div class="dashboard-card profile-card">
                <div class="dashboard-card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        @if($user->foto)
                            <img src="{{ Storage::url($user->foto) }}" alt="Foto Profil" class="profile-avatar">
                        @else
                            <div class="avatar-placeholder d-flex align-items-center justify-content-center">
                                <span>{{ strtoupper(substr($siswa->nama_siswa ?? 'S', 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $siswa->nama_siswa ?? 'Nama Siswa' }}</h5>
                    <p class="mb-0 text-muted">{{ $siswa->jurusan->nama_jurusan ?? 'Jurusan' }}</p>

                    <ul class="profile-info-list text-start">
                        <li><i class="bi bi-person-badge icon"></i> <span class="value">{{ $siswa->nis ?? 'N/A' }}</span></li>
                        <li><i class="bi bi-easel2 icon"></i> <span class="value">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</span></li>
                        <li><i class="bi bi-envelope-at icon"></i> <span class="value">{{ $user->email ?? 'N/A' }}</span></li>
                        <li><i class="bi bi-telephone icon"></i> <span class="value">{{ $siswa->kontak_siswa ?? 'N/A' }}</span></li>
                    </ul>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">Navigasi Cepat</div>
                <div class="dashboard-card-body p-2">
                    <nav class="nav flex-column quick-nav">
                        <a class="nav-link" href="{{ route('pengguna.pengajuan') }}"><i class="bi bi-buildings icon"></i> Daftar Perusahaan</a>
                        <a class="nav-link" href="{{ route('pengguna.ajukan-perusahaan-baru') }}"><i class="bi bi-building-add icon"></i> Ajukan Perusahaan Baru</a>
                     
                       
                    </nav>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="d-flex flex-column gap-4">
            <!-- Hero Status Card -->
            @if($prakerinData->isNotEmpty())
                <div class="hero-status-card bg-success shadow-lg">
                    <h5 class="text-white">Status Saat Ini</h5>
                    <h3 class="text-white">Aktif Magang</h3>
                    <p class="mb-0">Di <strong>{{ $prakerinData->first()->perusahaan->nama_perusahaan ?? 'N/A' }}</strong>. Semangat!</p>
                </div>
            @elseif($pengajuanDiterimaData->isNotEmpty())
                <div class="hero-status-card bg-primary shadow-lg">
                    <h5 class="text-white">Status Saat Ini</h5>
                    <h3 class="text-white">Pengajuan Diterima</h3>
                    <p class="mb-0">Oleh <strong>{{ $pengajuanDiterimaData->first()->perusahaan->nama_perusahaan ?? 'N/A' }}</strong>. Segera persiapkan dirimu!</p>
                </div>
            @else
                <div class="hero-status-card bg-secondary shadow-lg">
                    <h5 class="text-white">Status Saat Ini</h5>
                    <h3 class="text-white">Mencari Tempat Magang</h3>
                    <p class="mb-0">Ayo mulai perjalanan magangmu sekarang!</p>
                 
            @if($prakerinData->isEmpty() && $pengajuanDiterimaData->isEmpty())
                <a href="{{ route('pengguna.pengajuan') }}" class="btn btn-primary shadow-sm mt-3"><i class="bi bi-search me-2"></i>Cari Tempat Magang</a>
            @endif
        
                </div>
            @endif

            <!-- Prakerin Selesai dan Opsi Perpanjangan -->
            @if($prakerinData->isNotEmpty())
                @php
                    $prakerinSelesai = $prakerinData->where('status_prakerin', 'selesai');
                @endphp
                @if($prakerinSelesai->isNotEmpty())
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <i class="bi bi-check-circle-fill me-2 text-success"></i>
                            Prakerin Selesai - Opsi Perpanjangan
                        </div>
                        <div class="dashboard-card-body">
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Fitur Perpanjangan Prakerin:</strong> Anda dapat memperpanjang prakerin di perusahaan yang sudah diselesaikan dengan pembimbing yang sama.
                            </div>
                            
                            @foreach($prakerinSelesai as $prakerin)
                                <div class="card border-success mb-3">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title text-success mb-0">
                                                        <i class="bi bi-buildings me-2"></i>
                                                        {{ $prakerin->perusahaan->nama_perusahaan }}
                                                    </h6>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Selesai
                                                    </span>
                                                </div>
                                                <p class="card-text text-muted mb-2">
                                                    <i class="bi bi-calendar-range me-2"></i>
                                                    {{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') }} - 
                                                    {{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-2"></i>
                                                    Pembimbing: {{ $prakerin->pembimbingPerusahaan->nama ?? 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <div class="btn-group-vertical w-100">
                                                    <button class="btn btn-primary btn-sm mb-2" 
                                                            wire:click="bukaModalPerpanjangan({{ $prakerin->id_prakerin }})">
                                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                                        Perpanjang Prakerin
                                                    </button>
                                                    <button class="btn btn-success btn-sm mb-2" 
                                                            wire:click="kirimFormPenilaian({{ $prakerin->id_prakerin }})" 
                                                            wire:loading.attr="disabled">
                                                        <i class="bi bi-envelope me-2"></i>
                                                        Kirim Form Penilaian
                                                    </button>
                                                    <a href="{{ route('pengguna.riwayat-prakerin') }}" class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-list-ul me-2"></i>
                                                        Lihat Riwayat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Statistics -->
            <div class="stat-grid">
                <div class="stat-card"><div class="stat-number">{{ $totalPengajuan }}</div><div class="stat-label">Total Pengajuan</div></div>
                <div class="stat-card"><div class="stat-number text-success">{{ $diterimaCount }}</div><div class="stat-label">Diterima</div></div>
                <div class="stat-card"><div class="stat-number text-danger">{{ $ditolakCount }}</div><div class="stat-label">Ditolak</div></div>
                <div class="stat-card"><div class="stat-number text-warning">{{ $pendingCount }}</div><div class="stat-label">Diproses</div></div>
            </div>

            <!-- Main Info Card (Dynamic Content) -->
            @if($prakerinData->isNotEmpty())
                <!-- Detail Magang Aktif -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                        <span>Detail Magang Aktif</span>
                        <span class="badge bg-primary">
                            <i class="bi bi-play-circle me-1"></i>
                            Sedang Berlangsung
                        </span>
                    </div>
                    <div class="dashboard-card-body">
                         @foreach($prakerinData as $prakerin)
                            @php
                                $startDate = \Carbon\Carbon::parse($prakerin->tanggal_mulai);
                                $endDate = \Carbon\Carbon::parse($prakerin->tanggal_selesai);
                                $now = \Carbon\Carbon::now();
                                
                                // Hitung total durasi dalam hari
                                $totalDays = $startDate->diffInDays($endDate) + 1;
                                
                                // Hitung progress berdasarkan posisi hari ini
                                if ($now->isBefore($startDate)) {
                                    // Belum mulai
                                    $progressPercentage = 0;
                                } elseif ($now->isAfter($endDate)) {
                                    // Sudah selesai
                                    $progressPercentage = 100;
                                } else {
                                    // Sedang berlangsung
                                    $daysPassed = $startDate->diffInDays($now) + 1;
                                    $progressPercentage = min(100, ($daysPassed / $totalDays) * 100);
                                }
                            @endphp
                            <h6 class="mb-3 fw-bold text-primary">Progres Durasi Magang</h6>
                            <div class="progress" role="progressbar" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: {{ $progressPercentage }}%">{{ round($progressPercentage) }}%</div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                <span>Mulai: {{ $startDate->format('d M Y') }}</span>
                                <span>Selesai: {{ $endDate->format('d M Y') }}</span>
                            </div>
                            <hr class="my-4">
                            <h6 class="mb-3 fw-bold text-primary">Informasi Lengkap</h6>
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <ul class="list-group list-group-flush info-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-buildings me-2"></i>Nama Perusahaan</span>
                                            <strong>{{ $prakerin->perusahaan->nama_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                                            <strong class="text-end">{{ $prakerin->perusahaan->alamat_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                                            <strong>{{ $prakerin->perusahaan->email_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-telephone me-2"></i>Telepon</span>
                                            <strong>{{ $prakerin->perusahaan->kontak_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3 fw-semibold">Pembimbing Perusahaan</h6>
                                    <ul class="list-group list-group-flush info-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-person me-2"></i>Nama</span>
                                            <strong>{{ $prakerin->pembimbingPerusahaan->nama ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-telephone me-2"></i>Kontak</span>
                                            <strong>{{ $prakerin->pembimbingPerusahaan->no_hp ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                                            <strong>{{ $prakerin->pembimbingPerusahaan->email ?? 'N/A' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3 fw-semibold">Pembimbing Sekolah</h6>
                                    <ul class="list-group list-group-flush info-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-person-video3 me-2"></i>Nama</span>
                                            <strong>{{ $prakerin->perusahaan->pembimbingSekolah->nama_pembimbing_sekolah ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-telephone me-2"></i>Kontak</span>
                                            <strong>{{ $prakerin->perusahaan->pembimbingSekolah->kontak_pembimbing_sekolah ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                                            <strong>{{ $prakerin->perusahaan->pembimbingSekolah->email_pembimbing_sekolah ?? 'N/A' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($pengajuanDiterimaData->isNotEmpty())
                <!-- Info Magang Diterima -->
                <div class="dashboard-card">
                    <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                        <span>Informasi Magang Diterima</span>
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Diterima
                        </span>
                    </div>
                    <div class="dashboard-card-body">
                        <p>Selamat! Pengajuan Anda ke perusahaan berikut telah diterima. Mohon persiapkan diri Anda sesuai jadwal yang ditentukan.</p>
                        @foreach($pengajuanDiterimaData as $pengajuan)
                            @php
                                $startDate = \Carbon\Carbon::parse($pengajuan->tanggal_mulai);
                                $endDate = \Carbon\Carbon::parse($pengajuan->tanggal_selesai);
                                $now = \Carbon\Carbon::now();
                                
                                // Hitung total durasi dalam hari
                                $totalDays = $startDate->diffInDays($endDate) + 1;
                                
                                // Hitung progress persiapan (berapa hari lagi sampai mulai)
                                if ($now->isAfter($startDate)) {
                                    // Sudah mulai, progress 100%
                                    $progressPercentage = 100;
                                } else {
                                    // Belum mulai, hitung progress persiapan
                                    $daysUntilStart = $now->diffInDays($startDate);
                                    $preparationPeriod = min(30, $totalDays); // Maksimal 30 hari persiapan
                                    $progressPercentage = max(0, min(100, (($preparationPeriod - $daysUntilStart) / $preparationPeriod) * 100));
                                }
                            @endphp
                            
                            <h6 class="mb-3 fw-bold text-primary">Progres Persiapan Magang</h6>
                            <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: {{ $progressPercentage }}%">{{ round($progressPercentage) }}%</div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 small text-muted">
                                <span>Mulai: {{ $startDate->format('d M Y') }}</span>
                                <span>Selesai: {{ $endDate->format('d M Y') }}</span>
                            </div>
                            
                            <hr class="my-4">
                            <h6 class="mb-3 fw-bold text-primary">Informasi Lengkap Perusahaan</h6>
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <ul class="list-group list-group-flush info-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-buildings me-2"></i>Nama Perusahaan</span>
                                            <strong>{{ $pengajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                                            <strong class="text-end">{{ $pengajuan->perusahaan->alamat_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                                            <strong>{{ $pengajuan->perusahaan->email_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-telephone me-2"></i>Telepon</span>
                                            <strong>{{ $pengajuan->perusahaan->kontak_perusahaan ?? 'N/A' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3 fw-semibold">Pembimbing Perusahaan</h6>
                                    <ul class="list-group list-group-flush info-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-person me-2"></i>Nama</span>
                                            <strong>{{ $pengajuan->perusahaan->pembimbingPerusahaan->first()?->nama ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-telephone me-2"></i>Kontak</span>
                                            <strong>{{ $pengajuan->perusahaan->pembimbingPerusahaan->first()?->no_hp ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                                            <strong>{{ $pengajuan->perusahaan->pembimbingPerusahaan->first()?->email ?? 'N/A' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-3 fw-semibold">Pembimbing Sekolah</h6>
                                    <ul class="list-group list-group-flush info-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-person-video3 me-2"></i>Nama</span>
                                            <strong>{{ $pengajuan->perusahaan->pembimbingSekolah->nama_pembimbing_sekolah ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-telephone me-2"></i>Kontak</span>
                                            <strong>{{ $pengajuan->perusahaan->pembimbingSekolah->kontak_pembimbing_sekolah ?? 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="bi bi-envelope me-2"></i>Email</span>
                                            <strong>{{ $pengajuan->perusahaan->pembimbingSekolah->email_pembimbing_sekolah ?? 'N/A' }}</strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="alert alert-info mt-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Silakan hubungi pihak sekolah atau perusahaan jika ada pertanyaan lebih lanjut mengenai persiapan magang.
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Aktivitas Pengajuan Terbaru -->
            <div class="dashboard-card">
                <div class="dashboard-card-header">Aktivitas Pengajuan Terbaru</div>
                <div class="list-group list-group-flush activity-list p-0">
                    @forelse($recentPengajuan as $pengajuan)
                        <div class="list-group-item" @if($pengajuan->status_pengajuan !== 'pending') style="cursor:pointer" onclick="window.location='{{ route('pengguna.pengajuan.proses', ['id_perusahaan' => $pengajuan->id_perusahaan]) }}'" @endif>
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $pengajuan->perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</h6>
                                <small class="text-muted">{{ $pengajuan->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex w-100 justify-content-between align-items-center mt-1">
                                <small class="text-muted">Diajukan pada: {{ $pengajuan->created_at->format('d M Y') }}</small>
                                <span class="badge rounded-pill @switch($pengajuan->status_pengajuan) @case('pending') bg-warning @break @case('diterima_admin') bg-info @break @case('diterima_perusahaan') bg-success @break @case('ditolak_admin') @case('ditolak_perusahaan') bg-danger @break @default bg-secondary @break @endswitch">
                                    @switch($pengajuan->status_pengajuan)
                                        @case('pending') Menunggu Admin @break
                                        @case('diterima_admin') Diproses Perusahaan @break
                                        @case('diterima_perusahaan') Diterima @break
                                        @case('ditolak_admin') Ditolak Admin @break
                                        @case('ditolak_perusahaan') Ditolak Perusahaan @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-2"></i>
                            <p class="mt-2">Belum ada aktivitas pengajuan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-danger text-center">
    <h4 class="alert-heading">Data Siswa Tidak Ditemukan!</h4>
    <p>Data siswa yang terhubung dengan akun Anda tidak dapat ditemukan. Mohon segera hubungi administrator sekolah untuk pengecekan lebih lanjut.</p>
</div>
@endif

<!-- Modal Perpanjangan Prakerin -->
@if($showModalPerpanjangan ?? false)
<div class="modal fade show" style="display: block;" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-clockwise me-2"></i>Perpanjang Prakerin
                </h5>
                <button type="button" class="btn-close" wire:click="tutupModalPerpanjangan"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="prosesPerpanjangan">
                    <div class="mb-3">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" class="form-control" value="{{ $perusahaanSelesai->where('id_perusahaan', $selectedPerusahaanId ?? '')->first()->nama_perusahaan ?? 'N/A' }}" readonly>
                        <small class="text-muted">Perusahaan yang sudah diselesaikan sebelumnya</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggalMulaiPerpanjangan" class="form-label">Tanggal Mulai Perpanjangan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggalMulaiPerpanjangan') is-invalid @enderror" 
                                   id="tanggalMulaiPerpanjangan" wire:model.defer="tanggalMulaiPerpanjangan">
                            @error('tanggalMulaiPerpanjangan') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggalSelesaiPerpanjangan" class="form-label">Tanggal Selesai Perpanjangan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggalSelesaiPerpanjangan') is-invalid @enderror" 
                                   id="tanggalSelesaiPerpanjangan" wire:model.defer="tanggalSelesaiPerpanjangan">
                            @error('tanggalSelesaiPerpanjangan') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keteranganPerpanjangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control @error('keteranganPerpanjangan') is-invalid @enderror" 
                                  id="keteranganPerpanjangan" wire:model.defer="keteranganPerpanjangan" 
                                  rows="3" placeholder="Tambahkan keterangan tentang perpanjangan prakerin..."></textarea>
                        @error('keteranganPerpanjangan') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi:</strong> Perpanjangan prakerin akan menggunakan pembimbing dan perusahaan yang sama dengan prakerin sebelumnya.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="tutupModalPerpanjangan">
                    <i class="bi bi-x-circle me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" wire:click="prosesPerpanjangan" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="prosesPerpanjangan">
                        <i class="bi bi-check-circle me-2"></i>Perpanjang Prakerin
                    </span>
                    <span wire:loading wire:target="prosesPerpanjangan" class="spinner-border spinner-border-sm me-2"></span>
                    Memproses...
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
@endif