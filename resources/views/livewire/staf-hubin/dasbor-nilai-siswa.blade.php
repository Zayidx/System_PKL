{{-- Ringkasan nilai PKL per siswa dalam satu kelas beserta modal detail. --}}
<div>
    <a href="{{ route('staf-hubin.data.prakerin.kelas', ['id_kelas' => $kelas->id_kelas]) }}" class="btn btn-link p-2 mb-3 bg-primary text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa</a>
    
    <div class="row mb-4">
        <div class="col-md-3 text-center">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h4 class="mb-1">{{ $kelas->nama_kelas }}</h4>
                    <small>{{ $kelas->jurusan->nama_jurusan_singkat ?? 'N/A' }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h4 class="mb-1">Nilai PKL Siswa</h4>
            <p class="text-muted mb-0">Monitoring nilai PKL siswa kelas {{ $kelas->nama_kelas }}</p>
        </div>
    </div>

    <!-- Search dan Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex gap-2">
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama siswa atau NIS...">
                <select wire:model.live="perPage" class="form-select" style="width: auto;">
                    <option value="5">5 per halaman</option>
                    <option value="10">10 per halaman</option>
                    <option value="20">20 per halaman</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Daftar Siswa dengan Nilai -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-star-fill text-warning me-2"></i>
                Daftar Siswa dengan Nilai PKL
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <a href="#" wire:click.prevent="setSortBy('nama_siswa')" class="text-decoration-none text-dark">
                                    Nama Siswa
                                    @if($sortBy === 'nama_siswa')
                                        <i class="bi bi-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="#" wire:click.prevent="setSortBy('nis')" class="text-decoration-none text-dark">
                                    NIS
                                    @if($sortBy === 'nis')
                                        <i class="bi bi-arrow-{{ $sortDir === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Prakerin Selesai</th>
                            <th>Prakerin Dinilai</th>
                            <th>Rata-rata Nilai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswaList as $siswa)
                            @php
                                $prakerinSelesai = $siswa->prakerin()->where('status_prakerin', 'selesai')->get();
                                $prakerinDinilai = $prakerinSelesai->filter(function($prakerin) use ($siswa) {
                                    return $prakerin->pembimbingPerusahaan->penilaian()
                                        ->where('nis_siswa', $siswa->nis)
                                        ->exists();
                                });
                                
                                $nilaiRataRata = 0;
                                if($prakerinDinilai->count() > 0) {
                                    $totalNilai = 0;
                                    $totalKompetensi = 0;
                                    foreach($prakerinDinilai as $prakerin) {
                                        $penilaian = $prakerin->pembimbingPerusahaan->penilaian()
                                            ->where('nis_siswa', $siswa->nis)
                                            ->with('kompetensi')
                                            ->first();
                                        if($penilaian) {
                                            $totalNilai += $penilaian->kompetensi->sum('pivot.nilai');
                                            $totalKompetensi += $penilaian->kompetensi->count();
                                        }
                                    }
                                    $nilaiRataRata = $totalKompetensi > 0 ? $totalNilai / $totalKompetensi : 0;
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($siswa->user && $siswa->user->foto)
                                            <img src="{{ Storage::url($siswa->user->foto) }}" alt="Foto Siswa" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="avatar-placeholder rounded-circle me-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($siswa->nama_siswa ?? 'S', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $siswa->nama_siswa }}</div>
                                            <small class="text-muted">{{ $siswa->kontak_siswa ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $siswa->nis }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $siswa->prakerin_selesai }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $prakerinDinilai->count() > 0 ? 'success' : 'warning' }}">
                                        {{ $prakerinDinilai->count() }}
                                    </span>
                                </td>
                                <td>
                                    @if($nilaiRataRata > 0)
                                        <div class="d-flex align-items-center">
                                            <span class="fw-bold text-{{ $nilaiRataRata >= 85 ? 'success' : ($nilaiRataRata >= 75 ? 'warning' : 'danger') }}">
                                                {{ number_format($nilaiRataRata, 1) }}
                                            </span>
                                            <small class="text-muted ms-1">/ 100</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($prakerinSelesai->count() > 0)
                                        <button class="btn btn-primary btn-sm" wire:click="lihatDetailNilai('{{ $siswa->nis }}')">
                                            <i class="bi bi-eye me-1"></i>Lihat Nilai
                                        </button>
                                    @else
                                        <span class="text-muted">Belum ada prakerin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2"></i>
                                        Tidak ada siswa ditemukan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $siswaList->links() }}</div>
        </div>
    </div>

    <!-- Modal Detail Nilai -->
    @if($showDetailNilai && $selectedSiswa)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-star-fill text-warning me-2"></i>
                            Detail Nilai PKL - {{ $selectedSiswa->nama_siswa }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="tutupDetailNilai"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $prakerinSelesai = $selectedSiswa->prakerin()->where('status_prakerin', 'selesai')
                                ->with(['perusahaan', 'pembimbingPerusahaan'])
                                ->get();
                        @endphp

                        @if($prakerinSelesai->count() > 0)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Informasi Siswa</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Nama:</strong></td>
                                            <td>{{ $selectedSiswa->nama_siswa }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIS:</strong></td>
                                            <td>{{ $selectedSiswa->nis }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kelas:</strong></td>
                                            <td>{{ $selectedSiswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jurusan:</strong></td>
                                            <td>{{ $selectedSiswa->jurusan->nama_jurusan_singkat ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Ringkasan Nilai</h6>
                                    @php
                                        $totalNilai = 0;
                                        $totalKompetensi = 0;
                                        $prakerinDinilai = 0;
                                        foreach($prakerinSelesai as $prakerin) {
                                            $penilaian = $prakerin->pembimbingPerusahaan->penilaian()
                                                ->where('nis_siswa', $selectedSiswa->nis)
                                                ->with('kompetensi')
                                                ->first();
                                            if($penilaian) {
                                                $totalNilai += $penilaian->kompetensi->sum('pivot.nilai');
                                                $totalKompetensi += $penilaian->kompetensi->count();
                                                $prakerinDinilai++;
                                            }
                                        }
                                        $nilaiRataRata = $totalKompetensi > 0 ? $totalNilai / $totalKompetensi : 0;
                                    @endphp
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="fw-bold text-primary">{{ $prakerinSelesai->count() }}</div>
                                                    <small>Total Prakerin</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold text-success">{{ $prakerinDinilai }}</div>
                                                    <small>Sudah Dinilai</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold text-{{ $nilaiRataRata >= 85 ? 'success' : ($nilaiRataRata >= 75 ? 'warning' : 'danger') }}">
                                                        {{ number_format($nilaiRataRata, 1) }}
                                                    </div>
                                                    <small>Rata-rata</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Nilai per Prakerin -->
                            <h6 class="fw-bold mb-3">Detail Nilai per Prakerin</h6>
                            @foreach($prakerinSelesai as $index => $prakerin)
                                @php
                                    $penilaian = $prakerin->pembimbingPerusahaan->penilaian()
                                        ->where('nis_siswa', $selectedSiswa->nis)
                                        ->with('kompetensi')
                                        ->first();
                                @endphp
                                
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="bi bi-building me-2"></i>
                                            {{ $prakerin->perusahaan->nama_perusahaan }}
                                            <span class="badge bg-{{ $penilaian ? 'success' : 'warning' }} ms-2">
                                                {{ $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai' }}
                                            </span>
                                        </h6>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') }}
                                        </small>
                                    </div>
                                    <div class="card-body">
                                        @if($penilaian)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Kompetensi</th>
                                                            <th class="text-center">Nilai</th>
                                                            <th class="text-center">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($penilaian->kompetensi as $kompetensi)
                                                            <tr>
                                                                <td>{{ $kompetensi->nama_kompetensi }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-{{ $kompetensi->pivot->nilai >= 85 ? 'success' : ($kompetensi->pivot->nilai >= 75 ? 'warning' : 'danger') }}">
                                                                        {{ $kompetensi->pivot->nilai }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if($kompetensi->pivot->nilai >= 85)
                                                                        <span class="text-success">Sangat Baik</span>
                                                                    @elseif($kompetensi->pivot->nilai >= 75)
                                                                        <span class="text-warning">Baik</span>
                                                                    @else
                                                                        <span class="text-danger">Perlu Perbaikan</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-2">
                                                <strong>Rata-rata: </strong>
                                                <span class="badge bg-primary">
                                                    {{ number_format($penilaian->kompetensi->avg('pivot.nilai'), 1) }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-center py-3">
                                                <i class="bi bi-clock text-warning fs-1 d-block mb-2"></i>
                                                <p class="text-muted mb-0">Pembimbing perusahaan belum memberikan penilaian</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-exclamation-triangle text-warning fs-1 d-block mb-3"></i>
                                <h6>Belum Ada Prakerin Selesai</h6>
                                <p class="text-muted">Siswa ini belum menyelesaikan prakerin apapun.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="tutupDetailNilai">
                            <i class="bi bi-x-circle me-2"></i>Tutup
                        </button>
                        @if($prakerinSelesai->count() > 0)
                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Cetak
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div> 
