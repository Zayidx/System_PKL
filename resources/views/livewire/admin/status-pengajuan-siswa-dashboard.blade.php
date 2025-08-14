<div class="">
    <div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb my-0">
                <li class="breadcrumb-item"><a href="{{ route('staffhubin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('staffhubin.master.pengajuan.siswa', ['id_kelas' => $siswa->kelas->id_kelas]) }}">Daftar Siswa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Status Pengajuan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    @if($siswa->user && $siswa->user->foto)
                        <img src="{{ Storage::url($siswa->user->foto) }}" alt="Foto Siswa" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="avatar-placeholder rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center bg-primary text-white" style="width: 120px; height: 120px; font-size: 3rem;">
                            {{ strtoupper(substr($siswa->nama_siswa ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <h4 class="mb-1">{{ $siswa->nama_siswa }}</h4>
                    <p class="text-muted mb-2">NIS: {{ $siswa->nis }}</p>
                    <a href="{{ route('staffhubin.master.nilai.siswa', ['id_kelas' => $siswa->kelas->id_kelas]) }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-star-fill me-1"></i> Cek Nilai PKL
                    </a>
                </div>
                <div class="card-footer bg-white">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"><strong>Kelas:</strong> <span>{{ $siswa->kelas->nama_kelas ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Jurusan:</strong> <span>{{ $siswa->jurusan->nama_jurusan_singkat ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Kontak:</strong> <span>{{ $siswa->kontak_siswa ?? '-' }}</span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Email:</strong> <span>{{ $siswa->user->email ?? '-' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Riwayat Pengajuan</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                            <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                            <span class="text-muted d-none d-md-block my-auto">per halaman</span>
                        </div>
                        <input type="search" wire:model.live.debounce.300ms="search" class="form-control form-control-sm" style="width: 250px;" placeholder="Cari perusahaan...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Perusahaan</th>
                                    <th>Tgl. Pengajuan</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengajuanTerdaftar as $p)
                                    <tr wire:key="{{ $p->id_pengajuan }}">
                                        <td class="text-center">{{ $p->id_pengajuan }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $p->perusahaan->nama_perusahaan ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $p->is_perusahaan_terdaftar ? 'Terdaftar' : 'Baru' }}</small>
                                        </td>
                                        <td>{{ $p->created_at->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge fs-6
                                                @switch($p->status_pengajuan)
                                                    @case('pending') bg-light-warning text-warning @break
                                                    @case('ditolak_admin') bg-light-danger text-danger @break
                                                    @case('diterima_admin') bg-light-info text-info @break
                                                    @case('diterima_perusahaan') bg-light-success text-success @break
                                                    @case('ditolak_perusahaan') bg-light-danger text-danger @break
                                                    @default bg-light-secondary text-secondary @break
                                                @endswitch">
                                                @switch($p->status_pengajuan)
                                                    @case('pending') <i class="bi bi-clock-history me-1"></i> Menunggu @break
                                                    @case('ditolak_admin') <i class="bi bi-x-circle me-1"></i> Ditolak Admin @break
                                                    @case('diterima_admin') <i class="bi bi-send me-1"></i> Menunggu Perusahaan @break
                                                    @case('diterima_perusahaan') <i class="bi bi-check-circle me-1"></i> Diterima @break
                                                    @case('ditolak_perusahaan') <i class="bi bi-x-circle-fill me-1"></i> Ditolak Perusahaan @break
                                                    @default {{ ucfirst(str_replace('_', ' ', $p->status_pengajuan)) }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($p->status_pengajuan === 'pending')
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-success" wire:click="approvePengajuan({{ $p->id_pengajuan }})" wire:loading.attr="disabled" data-bs-toggle="tooltip" title="Setujui Pengajuan">
                                                        <span wire:loading.remove wire:target="approvePengajuan({{ $p->id_pengajuan }})"><i class="bi bi-check-lg"></i></span>
                                                        <span wire:loading wire:target="approvePengajuan({{ $p->id_pengajuan }})" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    </button>
                                                    <button class="btn btn-danger" wire:click="declinePengajuan({{ $p->id_pengajuan }})" wire:loading.attr="disabled" data-bs-toggle="tooltip" title="Tolak Pengajuan">
                                                        <span wire:loading.remove wire:target="declinePengajuan({{ $p->id_pengajuan }})"><i class="bi bi-x-lg"></i></span>
                                                        <span wire:loading wire:target="declinePengajuan({{ $p->id_pengajuan }})" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-folder-x fs-3 text-muted"></i>
                                            <h5 class="mt-2">Tidak Ada Riwayat Pengajuan</h5>
                                            <p class="text-muted">Siswa ini belum pernah mengajukan PKL.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $pengajuanTerdaftar->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@push('scripts')
<script>
    // Inisialisasi tooltip setelah render
    document.addEventListener('livewire:navigated', () => {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush

