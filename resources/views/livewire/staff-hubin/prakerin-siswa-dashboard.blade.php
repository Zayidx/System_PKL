<div>
    <a href="{{ route('staffhubin.master.prakerin') }}" class="btn btn-link p-2 mb-3 bg-primary text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Kelas</a>
    
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Daftar Siswa Prakerin di Kelas {{ $kelas->nama_kelas }}</h4>
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
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" style="width: 300px;" placeholder="Cari nama atau NIS siswa...">
            </div>

           <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Perusahaan</th>
                            <th>Pembimbing Perusahaan</th>
                            <th>Pembimbing Sekolah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $siswa)
                            <tr>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->prakerin->first()->perusahaan->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $siswa->prakerin->first()->pembimbingPerusahaan->nama ?? '-' }}</td>
                                <td>{{ $siswa->prakerin->first()->pembimbingSekolah->nama_pembimbing_sekolah ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staffhubin.master.prakerin.status', ['nis' => $siswa->nis]) }}" class="btn btn-primary btn-sm">
                                        Lihat Detail <span class="badge bg-white text-primary ms-1">{{ $siswa->prakerin_count }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada data siswa prakerin yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $siswaList->links() }}
            </div>
        </div>
    </div>
</div> 