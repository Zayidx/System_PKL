{{-- Monitoring prakerin per kelas dengan ringkasan jumlah siswa dan prakerin. --}}
<div wire:poll.10s>
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Daftar Kelas - Prakerin</h4>
            <div class="d-flex align-items-center gap-2">
                <div class="spinner-border spinner-border-sm text-primary" wire:loading role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <small class="text-muted">Auto-refresh setiap 10 detik</small>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 mb-3">
                <div class="d-flex gap-2">
                    <select wire:model.live="perPage" class="form-select" style="width: auto;">
                        <option value="5">5 per halaman</option>
                        <option value="10">10 per halaman</option>
                        <option value="20">20 per halaman</option>
                    </select>
                </div>
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" style="width: 300px;" placeholder="Cari nama kelas...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Jurusan</th>
                            <th>Total Siswa</th>
                            <th>Jumlah Prakerin</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelasList as $kelas)
                            <tr>
                                <td>{{ $kelas->nama_kelas }}</td>
                                <td>{{ $kelas->jurusan->nama_jurusan_singkat ?? '-' }}</td>
                                <td>{{ $kelas->total_siswa }}</td>
                                <td>{{ $kelas->prakerin_count }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staf-hubin.data.prakerin.kelas', ['id_kelas' => $kelas->id_kelas]) }}" class="btn btn-primary btn-sm">
                                        Lihat Siswa <span class="badge bg-white text-primary ms-1">{{ $kelas->total_siswa }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data kelas yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $kelasList->links() }}
            </div>
        </div>
    </div>
</div> 
