<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-clipboard-data me-2"></i>Daftar Penilaian PKL
            </h1>
            <a href="{{ route('admin.kompetensi') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Kompetensi
            </a>
        </div>

        <!-- Filter dan Pencarian -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Cari siswa atau pembimbing..."
                           wire:model.live="search">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" wire:model.live="filterJurusan">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusan as $j)
                        <option value="{{ $j->id_jurusan }}">
                            {{ $j->nama_jurusan_lengkap }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" wire:model.live="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="selesai">Selesai Dinilai</option>
                    <option value="belum_selesai">Belum Dinilai</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" wire:click="$refresh">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Tabel Penilaian -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>Daftar Penilaian PKL
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Informasi Siswa</th>
                                <th>Pembimbing & Perusahaan</th>
                                <th>Status Penilaian</th>
                                <th>Nilai Rata-rata</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penilaian as $index => $item)
                                <tr>
                                    <td>{{ $penilaian->firstItem() + $index }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $item->siswa->nama ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                NIS: {{ $item->siswa->nis ?? 'N/A' }}
                                            </small>
                                            <br>
                                            <span class="badge bg-info">
                                                {{ $item->siswa->kelas->jurusan->nama_jurusan_singkat ?? 'N/A' }}
                                            </span>
                                            <span class="badge bg-secondary">
                                                {{ $item->siswa->kelas->nama_kelas ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $item->pembimbingPerusahaan->nama ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $item->pembimbingPerusahaan->perusahaan->nama_perusahaan ?? 'N/A' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                {{ $item->pembimbingPerusahaan->jabatan ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->jumlah_kompetensi > 0)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Selesai
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $item->jumlah_kompetensi }} kompetensi dinilai
                                            </small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock me-1"></i>Belum Dinilai
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jumlah_kompetensi > 0)
                                            <div class="text-center">
                                                <h4 class="mb-0 text-{{ $item->nilai_rata_rata >= 85 ? 'success' : ($item->nilai_rata_rata >= 75 ? 'warning' : 'danger') }}">
                                                    {{ number_format($item->nilai_rata_rata, 1) }}
                                                </h4>
                                                <small class="text-muted">
                                                    Tertinggi: {{ $item->nilai_tertinggi }} | Terendah: {{ $item->nilai_terendah }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->jumlah_kompetensi > 0)
                                            <a href="{{ route('admin.penilaian-pkl.detail', $item->id_penilaian) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i>Detail Nilai
                                            </a>
                                        @else
                                            <span class="text-muted">Belum ada nilai</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-4"></i>
                                            <p class="mt-2">Tidak ada data penilaian</p>
                                            @if($search || $filterJurusan || $filterStatus)
                                                <small>Coba ubah filter pencarian</small>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $penilaian->links() }}
                </div>
            </div>
        </div>
    </div>
</div> 