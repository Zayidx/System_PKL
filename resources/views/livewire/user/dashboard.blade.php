<div>
    <div class="row gy-4">
        {{-- Kolom Kiri: Profil Siswa --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    @if($user->foto)
                        <img src="{{ Storage::url($user->foto) }}" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="avatar-placeholder rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center bg-primary text-white" style="width: 120px; height: 120px;">
                            <span style="font-size: 3rem;">{{ strtoupper(substr($siswa->nama_siswa ?? 'S', 0, 1)) }}</span>
                        </div>
                    @endif
                    <h4 class="mb-0">{{ $siswa->nama_siswa ?? 'Nama Siswa' }}</h4>
                    <p class="text-muted">{{ $user->email ?? 'email@siswa.com' }}</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item p-4 d-flex justify-content-between"><span>NIS</span> <strong>{{ $siswa->nis ?? 'N/A' }}</strong></li>
                    <li class="list-group-item p-4 d-flex justify-content-between"><span>Kelas</span> <strong>{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</strong></li>
                    <li class="list-group-item p-4 d-flex justify-content-between"><span>Jurusan</span> <strong>{{ $siswa->jurusan->nama_jurusan_singkat ?? 'N/A' }}</strong></li>
                    <li class="list-group-item p-4 d-flex justify-content-between"><span>Kontak</span> <strong>{{ $siswa->kontak_siswa ?? 'N/A' }}</strong></li>
                    <li class="list-group-item p-4 d-flex justify-content-between"><span>Kelahiran</span> <strong>{{ $siswa->tempat_lahir ?? 'N/A' }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d M Y') }}</strong></li>
                </ul>
            </div>
        </div>

        {{-- Kolom Kanan: Statistik dan Aktivitas Terbaru --}}
        <div class="col-lg-8">
            {{-- Kartu Statistik --}}
            {{-- Kartu Statistik --}}
            <div class="row g-3  justify-content-center ">
                <div class="col-md-3 col-6">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body">
                            <h2 class="text-primary mb-0">{{ $totalPengajuan }}</h2>
                            <p class="text-muted small mb-0">Total Pengajuan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body">
                            <h2 class="text-success mb-0">{{ $diterimaCount }}</h2>
                            <p class="text-muted small mb-0">Diterima</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body">
                            <h2 class="text-danger mb-0">{{ $ditolakCount }}</h2>
                            <p class="text-muted small mb-0">Ditolak</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card text-center shadow-sm ">
                        <div class="card-body">
                            <h2 class="text-warning mb-0">{{ $pendingCount }}</h2>
                            <p class="text-muted small mb-0">Diproses</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Tabel Aktivitas Terbaru --}}
            <div class="card shadow-sm">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">Aktivitas Pengajuan Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($recentPengajuan as $pengajuan)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $pengajuan->perusahaan->nama_perusahaan ?? 'Nama Perusahaan' }}</div>
                                            <small class="text-muted">Diajukan pada: {{ $pengajuan->created_at->format('d M Y, H:i') }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge fs-6 fw-normal
                                                @switch($pengajuan->status_pengajuan)
                                                    @case('pending') bg-warning text-dark @break
                                                    @case('diterima_admin') bg-info text-dark @break
                                                    @case('diterima_perusahaan') bg-success @break
                                                    @case('ditolak_admin') @case('ditolak_perusahaan') bg-danger @break
                                                    @default bg-secondary @break
                                                @endswitch">
                                                @switch($pengajuan->status_pengajuan)
                                                    @case('pending') <i class="bi bi-clock-history me-1"></i> Menunggu Admin @break
                                                    @case('diterima_admin') <i class="bi bi-hourglass-split me-1"></i> Menunggu Perusahaan @break
                                                    @case('diterima_perusahaan') <i class="bi bi-check-circle-fill me-1"></i> Diterima @break
                                                    @case('ditolak_admin') <i class="bi bi-x-circle-fill me-1"></i> Ditolak Admin @break
                                                    @case('ditolak_perusahaan') <i class="bi bi-x-octagon-fill me-1"></i> Ditolak Perusahaan @break
                                                @endswitch
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4">
                                            <p class="mb-0 text-muted">Belum ada aktivitas pengajuan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
