<div>
    <a href="{{ route('staffhubin.master.pengajuan.siswa', ['id_kelas' => $siswa->kelas->id_kelas]) }}" class="btn btn-link p-2 mb-3 bg-primary text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa</a>
    
    <div class="card shadow-sm">
         <div class="card-header">
            <h4 class="mb-0">Riwayat Pengajuan - {{ $siswa->nama_siswa }} ({{ $siswa->nis }})</h4>
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
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" style="width: 300px;" placeholder="Cari nama perusahaan...">
            </div>

             <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th >ID </th>
                            <th>Perusahaan Tujuan </th>
                            <th >Status </th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuanData as $pengajuan)
                            <tr>
                                <td>{{ $pengajuan->id_pengajuan }}</td>
                                <td>{{ $pengajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge fs-6
                                        @switch($pengajuan->status_pengajuan)
                                            @case('pending') bg-warning text-dark @break
                                            @case('ditolak_admin') bg-danger @break
                                            @case('diterima_admin') bg-info text-dark @break
                                            @case('diterima_perusahaan') bg-success @break
                                            @case('ditolak_perusahaan') bg-danger @break
                                            @default bg-secondary @break
                                        @endswitch">
                                        @switch($pengajuan->status_pengajuan)
                                            @case('pending') Menunggu Konfirmasi Admin @break
                                            @case('ditolak_admin') Ditolak oleh Admin @break
                                            @case('diterima_admin') Menunggu Konfirmasi Perusahaan @break
                                            @case('diterima_perusahaan') Diterima oleh Perusahaan @break
                                            @case('ditolak_perusahaan') Ditolak oleh Perusahaan @break
                                            @default {{ ucfirst(str_replace('_', ' ', $pengajuan->status_pengajuan)) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($pengajuan->status_pengajuan === 'pending')
                                        <button class="btn btn-success btn-sm me-1" 
                                                wire:click="approvePengajuan({{ $pengajuan->id_pengajuan }})"
                                                wire:confirm="Anda yakin ingin menyetujui pengajuan ini?">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </button>
                                        <button class="btn btn-danger btn-sm" 
                                                wire:click="declinePengajuan({{ $pengajuan->id_pengajuan }})"
                                                wire:confirm="Anda yakin ingin menolak pengajuan ini?">
                                            <i class="bi bi-x-circle"></i> Tolak
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Tidak ada data pengajuan yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pengajuanData->links() }}
            </div>
        </div>
    </div>
</div>
