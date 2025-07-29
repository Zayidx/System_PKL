<div>
    {{-- Custom CSS untuk peningkatan UI/UX --}}
    <style>
        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .info-item .icon {
            font-size: 1.2rem;
            margin-right: 1rem;
            color: var(--bs-primary);
            width: 20px;
            text-align: center;
        }
        .info-item .info-content {
            display: flex;
            flex-direction: column;
        }
        .info-item .info-content strong {
            font-weight: 500;
            color: var(--bs-gray-600);
        }
        .info-item .info-content span {
            color: var(--bs-gray-800);
        }
        .card-header .bi {
            font-size: 1.5rem;
            vertical-align: middle;
        }
        .progress-container {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
    </style>

    <div class="row">
        <!-- Informasi Prakerin Aktif -->
        @if($prakerinData->count() > 0)
            @foreach($prakerinData as $prakerin)
                @php
                    $startDate = \Carbon\Carbon::parse($prakerin->tanggal_mulai);
                    $endDate = \Carbon\Carbon::parse($prakerin->tanggal_selesai);
                    $today = \Carbon\Carbon::now();
                    
                    $totalDays = $startDate->diffInDays($endDate);
                    $daysPassed = $startDate->diffInDays($today->isAfter($endDate) ? $endDate : $today);
                    if ($daysPassed < 0) $daysPassed = 0; // Jika belum mulai
                    
                    $progressPercentage = ($totalDays > 0) ? ($daysPassed / $totalDays) * 100 : 0;
                    if ($progressPercentage > 100) $progressPercentage = 100;

                    $daysRemaining = $today->diffInDays($endDate, false);
                @endphp
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-start border-success border-4">
                        <div class="card-header bg-transparent pb-0 border-0">
                            <h4 class="mb-0 text-success"><i class="bi bi-building-check me-2"></i>Status Magang Aktif</h4>
                            <hr>
                        </div>
                        <div class="card-body pt-2">
                            <div class="row">
                                <div class="col-lg-7">
                                    <h5 class="text-primary mb-3">{{ $prakerin->perusahaan->nama_perusahaan ?? 'N/A' }}</h5>
                                    <div class="info-item">
                                        <i class="bi bi-geo-alt-fill icon"></i>
                                        <div class="info-content">
                                            <strong>Alamat Perusahaan</strong>
                                            <span>{{ $prakerin->perusahaan->alamat_perusahaan ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-person-workspace icon"></i>
                                        <div class="info-content">
                                            <strong>Pembimbing Perusahaan</strong>
                                            <span>{{ $prakerin->pembimbingPerusahaan->nama ?? 'N/A' }} ({{ $prakerin->pembimbingPerusahaan->no_hp ?? 'No Kontak' }})</span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <i class="bi bi-person-video3 icon"></i>
                                        <div class="info-content">
                                            <strong>Pembimbing Sekolah</strong>
                                            <span>{{ $prakerin->pembimbingSekolah->nama_pembimbing_sekolah ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="progress-container">
                                        <div class="progress-info">
                                            <span class="fw-bold">Linimasa Magang</span>
                                            <span class="badge bg-light-success text-success">{{ number_format($progressPercentage, 0) }}% Selesai</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{ $progressPercentage }}%" aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress-info mt-2">
                                            <small class="text-muted">{{ $startDate->format('d M Y') }}</small>
                                            <small class="text-muted">{{ $endDate->format('d M Y') }}</small>
                                        </div>
                                         @if($daysRemaining >= 0)
                                            <p class="text-center mt-2 text-muted small">Sisa waktu magang: <strong>{{ $daysRemaining }} hari</strong></p>
                                        @else
                                            <p class="text-center mt-2 text-success small"><strong>Periode magang telah selesai.</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Informasi Pengajuan Diterima -->
        @if($pengajuanData->count() > 0)
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-start border-info border-4">
                    <div class="card-header bg-transparent">
                        <h4 class="mb-0 text-info"><i class="bi bi-file-earmark-check-fill me-2"></i>Pengajuan Diterima & Menunggu Proses</h4>
                    </div>
                    <div class="card-body">
                        @foreach($pengajuanData as $pengajuan)
                            <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light rounded">
                                <div>
                                    <h6 class="mb-0">{{ $pengajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}
                                    </small>
                                </div>
                                <div>
                                    @if($pengajuan->link_cv)
                                        <a href="{{ $pengajuan->link_cv }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-link-45deg me-1"></i>Lihat CV
                                        </a>
                                    @else
                                        <span class="badge bg-light-secondary">CV Tidak Ada</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                         <div class="alert alert-light-info mt-3 mb-0">
                            <i class="bi bi-info-circle-fill"></i>
                            Status akan berubah menjadi "Magang Aktif" setelah admin menginput data pembimbing dan detail lainnya.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Informasi Data Siswa -->
        @if(Auth::user()->siswa)
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Informasi Siswa</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="bi bi-person-vcard icon"></i>
                                    <div class="info-content"><strong>NIS</strong><span>{{ Auth::user()->siswa->nis }}</span></div>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-person icon"></i>
                                    <div class="info-content"><strong>Nama Lengkap</strong><span>{{ Auth::user()->siswa->nama_siswa }}</span></div>
                                </div>
                                 <div class="info-item">
                                    <i class="bi bi-calendar-date icon"></i>
                                    <div class="info-content"><strong>TTL</strong><span>{{ Auth::user()->siswa->tempat_lahir ?? 'N/A' }}, {{ Auth::user()->siswa->tanggal_lahir ? \Carbon\Carbon::parse(Auth::user()->siswa->tanggal_lahir)->format('d M Y') : 'N/A' }}</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="bi bi-building icon"></i>
                                    <div class="info-content"><strong>Kelas & Jurusan</strong><span>{{ Auth::user()->siswa->kelas->nama_kelas ?? 'N/A' }} - {{ Auth::user()->siswa->jurusan->nama_jurusan_lengkap ?? 'N/A' }}</span></div>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-envelope icon"></i>
                                    <div class="info-content"><strong>Email</strong><span>{{ Auth::user()->email }}</span></div>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-telephone icon"></i>
                                    <div class="info-content"><strong>Kontak</strong><span>{{ Auth::user()->siswa->kontak_siswa ?? 'N/A' }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Jika tidak ada data -->
        @if($prakerinData->count() == 0 && $pengajuanData->count() == 0)
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <img src="https://placehold.co/200x200/e8f0fe/7380de?text=Cari+Magang" alt="[Ilustrasi mencari magang]" class="mb-4" style="max-width: 180px; border-radius: 50%;">
                        <h4 class="text-muted mt-3">Anda Belum Memiliki Program Magang</h4>
                        <p class="text-muted">Ayo mulai cari tempat magang impianmu dan ajukan segera!</p>
                        <a href="{{ route('user.pengajuan') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-search me-2"></i>Cari & Ajukan Magang
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
