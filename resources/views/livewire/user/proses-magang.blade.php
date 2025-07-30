<div>
    @push('styles')
    <style>
        /* Custom styles untuk UI/UX Dashboard dengan Tab */
        .status-hero-card {
            border-radius: 1rem;
            padding: 2.5rem;
            margin-bottom: 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .status-hero-card.bg-active {
            background: linear-gradient(135deg, #28a745, #218838);
        }
        .status-hero-card.bg-accepted {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }
        .status-hero-card.bg-search {
            background: linear-gradient(135deg, #6c757d, #5a6268);
        }
        .status-hero-card .hero-icon {
            font-size: 3rem;
            opacity: 0.15;
            position: absolute;
            right: 2rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .status-hero-card h4 {
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .status-hero-card h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0;
        }

        .details-tabs .nav-tabs {
            border-bottom: 2px solid var(--bs-border-color);
        }
        .details-tabs .nav-link {
            font-weight: 600;
            color: var(--bs-gray-600);
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1.25rem;
            margin-bottom: -2px;
        }
        .details-tabs .nav-link.active {
            color: var(--bs-primary);
            border-bottom-color: var(--bs-primary);
            background-color: transparent;
        }
        .details-tabs .tab-content {
            padding: 2rem 0;
        }
        
        .content-body-card {
            background-color: var(--bs-card-bg);
            border-radius: 0.75rem;
            box-shadow: var(--bs-box-shadow-sm);
            padding: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.75rem;
        }

        .info-block h6 {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--bs-gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        .info-block p {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--bs-body-color);
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .info-block p i {
            flex-shrink: 0;
        }
        
        .section-divider {
            margin: 2rem 0;
            border-top: 1px dashed var(--bs-border-color);
        }

        /* Progress Bar Timeline */
        .progress-timeline .dates {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--bs-gray-600);
            margin-bottom: 0.5rem;
        }
        .progress {
            height: 10px;
            border-radius: 1rem;
        }
        .progress-info {
            margin-top: 0.75rem;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
        }
    </style>
    @endpush

    @if($siswa)
    <div>
        <!-- =============================================================== -->
        <!-- KARTU STATUS UTAMA (HERO) -->
        <!-- =============================================================== -->
        @if($prakerinData->isNotEmpty())
            <div class="status-hero-card bg-active">
                <i class="bi bi-building-check hero-icon"></i>
                <h4>Status Saat Ini</h4>
                <h2>Aktif Magang</h2>
                <p class="mb-0">di {{ $prakerinData->first()->perusahaan->nama_perusahaan ?? 'N/A' }}</p>
            </div>
        @elseif($pengajuanData->isNotEmpty())
            <div class="status-hero-card bg-accepted">
                <i class="bi bi-file-earmark-check hero-icon"></i>
                <h4>Status Saat Ini</h4>
                <h2>Pengajuan Diterima</h2>
                <p class="mb-0">Oleh {{ $pengajuanData->first()->perusahaan->nama_perusahaan ?? 'N/A' }}. Menunggu jadwal mulai.</p>
            </div>
        @else
            <div class="status-hero-card bg-search">
                <i class="bi bi-search-heart hero-icon"></i>
                <h4>Status Saat Ini</h4>
                <h2>Belum Ada Magang</h2>
                <p class="mb-0">Ayo mulai cari tempat magang impianmu sekarang!</p>
            </div>
        @endif

        <!-- =============================================================== -->
        <!-- NAVIGASI TAB -->
        <!-- =============================================================== -->
        <div class="details-tabs">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link @if($prakerinData->isEmpty() && $pengajuanData->isEmpty()) disabled @endif active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-tab-pane" type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">
                        <i class="bi bi-file-text me-2"></i>Detail Status
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                        <i class="bi bi-person-badge me-2"></i>Profil Siswa
                    </button>
                </li>
            </ul>

            <!-- =============================================================== -->
            <!-- KONTEN TAB -->
            <!-- =============================================================== -->
            <div class="tab-content" id="myTabContent">
                <!-- KONTEN TAB DETAIL STATUS -->
                <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                    <div class="content-body-card">
                        @if($prakerinData->isNotEmpty())
                            @foreach($prakerinData as $prakerin)
                                @php
                                    $startDate = \Carbon\Carbon::parse($prakerin->tanggal_mulai);
                                    $endDate = \Carbon\Carbon::parse($prakerin->tanggal_selesai);
                                    $totalDays = $startDate->diffInDays($endDate) + 1;
                                    $daysPassed = now()->startOfDay()->diffInDays($startDate);
                                    if (now()->isBefore($startDate)) { $daysPassed = 0; } 
                                    elseif(now()->isAfter($endDate)) { $daysPassed = $totalDays; }
                                    $progressPercentage = $totalDays > 0 ? min(100, ($daysPassed / $totalDays) * 100) : 0;
                                @endphp
                                <h6>Informasi Prakerin & Perusahaan</h6>
                                <div class="info-grid mb-4">
                                    <div class="info-block"><h6>Perusahaan</h6><p><i class="bi bi-buildings"></i><span>{{ $prakerin->perusahaan->nama_perusahaan ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Alamat</h6><p><i class="bi bi-geo-alt"></i><span>{{ $prakerin->perusahaan->alamat_perusahaan ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Pembimbing Perusahaan</h6><p><i class="bi bi-person-workspace"></i><span>{{ $prakerin->pembimbingPerusahaan->nama ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Kontak Pembimbing</h6><p><i class="bi bi-telephone"></i><span>{{ $prakerin->pembimbingPerusahaan->no_hp ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Pembimbing Sekolah</h6><p><i class="bi bi-person-video3"></i><span>{{ $prakerin->perusahaan->pembimbingSekolah->nama_pembimbing_sekolah ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Keterangan</h6><p><i class="bi bi-info-circle"></i><span>{{ $prakerin->keterangan ?? 'Tidak ada' }}</span></p></div>
                                </div>
                                <div class="section-divider"></div>
                                <h6>Progres Durasi Magang</h6>
                                <div class="progress-timeline">
                                    <div class="dates"><span>{{ $startDate->format('d M Y') }}</span><span>{{ $endDate->format('d M Y') }}</span></div>
                                    <div class="progress" role="progressbar"><div class="progress-bar bg-success" style="width: {{ $progressPercentage }}%"></div></div>
                                    <div class="progress-info">Telah berjalan <strong>{{ $daysPassed }}</strong> dari <strong>{{ $totalDays }}</strong> hari.</div>
                                </div>
                            @endforeach
                        @elseif($pengajuanData->isNotEmpty())
                            @foreach($pengajuanData as $pengajuan)
                                <h5 class="mb-3">{{ $pengajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</h5>
                                <div class="info-grid">
                                    <div class="info-block"><h6>Alamat</h6><p><i class="bi bi-geo-alt"></i><span>{{ $pengajuan->perusahaan->alamat_perusahaan ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Email</h6><p><i class="bi bi-envelope"></i><span>{{ $pengajuan->perusahaan->email_perusahaan ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Kontak</h6><p><i class="bi bi-telephone"></i><span>{{ $pengajuan->perusahaan->kontak_perusahaan ?? 'N/A' }}</span></p></div>
                                    <div class="info-block"><h6>Jadwal Magang</h6><p><i class="bi bi-calendar-range"></i><span>{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}</span></p></div>
                                    <div class="info-block"><h6>Durasi</h6><p><i class="bi bi-clock-history"></i><span>{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($pengajuan->tanggal_selesai)) + 1 }} hari</span></p></div>
                                    <div class="info-block"><h6>Pembimbing Sekolah</h6><p><i class="bi bi-person-video3"></i><span>{{ $pengajuan->perusahaan->pembimbingSekolah->nama_pembimbing_sekolah ?? 'Belum ditugaskan' }}</span></p></div>
                                    <div class="info-block"><h6>CV Anda</h6>
                                        @if($pengajuan->link_cv)<a href="{{ $pengajuan->link_cv }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-link-45deg me-1"></i> Lihat CV</a>
                                        @else <p class="text-muted">Tidak ada</p> @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-info-circle-fill text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Tidak Ada Detail Status</h5>
                                <p class="text-muted">Silakan ajukan magang untuk melihat detailnya di sini.</p>
                                <a href="{{ route('user.pengajuan') }}" class="btn btn-primary mt-2"><i class="bi bi-send-plus-fill me-2"></i>Ajukan Magang</a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- KONTEN TAB PROFIL SISWA -->
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="content-body-card">
                        <div class="info-grid">
                            <div class="info-block"><h6>Nama</h6><p><span>{{ $siswa->nama_siswa }}</span></p></div>
                            <div class="info-block"><h6>NIS</h6><p><span>{{ $siswa->nis }}</span></p></div>
                            <div class="info-block"><h6>Kelas & Jurusan</h6><p><span>{{ $siswa->kelas->nama_kelas ?? 'N/A' }} - {{ $siswa->jurusan->nama_jurusan_lengkap ?? 'N/A' }}</span></p></div>
                            <div class="info-block"><h6>Kontak</h6><p><span>{{ $siswa->kontak_siswa ?? 'N/A' }}</span></p></div>
                            <div class="info-block"><h6>Email</h6><p><span>{{ Auth::user()->email }}</span></p></div>
                            <div class="info-block"><h6>Lahir</h6><p><span>{{ $siswa->tempat_lahir ?? 'N/A' }}, {{ $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') : 'N/A' }}</span></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-12">
        <div class="alert alert-danger text-center">
            <h4 class="alert-heading">Data Tidak Ditemukan!</h4>
            <p>Data siswa yang terhubung dengan akun Anda tidak dapat ditemukan. Mohon hubungi administrator untuk verifikasi data.</p>
        </div>
    </div>
    @endif
</div>
