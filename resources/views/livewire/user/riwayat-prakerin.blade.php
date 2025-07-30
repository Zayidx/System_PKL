<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Riwayat Prakerin</h4>
                <p class="text-muted mb-0">Kelola riwayat prakerin dan pengajuan Anda</p>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="riwayatTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="prakerin-tab" data-bs-toggle="tab" data-bs-target="#prakerin" type="button" role="tab">
                    <i class="bi bi-briefcase me-2"></i>Riwayat Prakerin
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pengajuan-tab" data-bs-toggle="tab" data-bs-target="#pengajuan" type="button" role="tab">
                    <i class="bi bi-file-earmark-text me-2"></i>Riwayat Pengajuan
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="riwayatTabsContent">
            <!-- Tab Prakerin -->
            <div class="tab-pane fade show active" id="prakerin" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari perusahaan atau status...">
                                <select wire:model.live="perPage" class="form-select" style="width: auto;">
                                    <option value="5">5 per halaman</option>
                                    <option value="10">10 per halaman</option>
                                    <option value="20">20 per halaman</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Perusahaan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Pembimbing Sekolah</th>
                                        <th>Pembimbing Perusahaan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($prakerinData as $index => $prakerin)
                                        <tr>
                                            <td>{{ $prakerinData->firstItem() + $index }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $prakerin->perusahaan->nama_perusahaan }}</div>
                                                <small class="text-muted">{{ $prakerin->perusahaan->alamat_perusahaan }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') }}</td>
                                            <td>
                                                @if($prakerin->status_prakerin === 'aktif')
                                                    <span class="badge bg-success">Aktif</span>
                                                @elseif($prakerin->status_prakerin === 'selesai')
                                                    <span class="badge bg-info">Selesai</span>
                                                @else
                                                    <span class="badge bg-secondary">Dibatalkan</span>
                                                @endif
                                            </td>
                                            <td>{{ $prakerin->pembimbingSekolah->nama_pembimbing_sekolah ?? 'N/A' }}</td>
                                            <td>{{ $prakerin->pembimbingPerusahaan->nama ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                @if($prakerin->status_prakerin === 'aktif')
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-success btn-sm" wire:click="selesaiPrakerin({{ $prakerin->id_prakerin }})" wire:loading.attr="disabled">
                                                            <i class="bi bi-check-circle"></i> Selesai
                                                        </button>
                                                        <button class="btn btn-warning btn-sm" wire:click="batalkanPrakerin({{ $prakerin->id_prakerin }})" wire:loading.attr="disabled">
                                                            <i class="bi bi-x-circle"></i> Batalkan
                                                        </button>
                                                    </div>
                                                @elseif($prakerin->status_prakerin === 'selesai')
                                                    <button class="btn btn-primary btn-sm" wire:click="ajukanKembali({{ $prakerin->perusahaan->id_perusahaan }})">
                                                        <i class="bi bi-arrow-repeat"></i> Ajukan Kembali
                                                    </button>
                                                @else
                                                    <button class="btn btn-primary btn-sm" wire:click="ajukanKembali({{ $prakerin->perusahaan->id_perusahaan }})">
                                                        <i class="bi bi-arrow-repeat"></i> Ajukan Kembali
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    Belum ada riwayat prakerin
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $prakerinData->links() }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab Pengajuan -->
            <div class="tab-pane fade" id="pengajuan" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari perusahaan atau status...">
                                <select wire:model.live="perPage" class="form-select" style="width: auto;">
                                    <option value="5">5 per halaman</option>
                                    <option value="10">10 per halaman</option>
                                    <option value="20">20 per halaman</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Perusahaan</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pengajuanData as $index => $pengajuan)
                                        <tr>
                                            <td>{{ $pengajuanData->firstItem() + $index }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $pengajuan->perusahaan->nama_perusahaan }}</div>
                                                <small class="text-muted">{{ $pengajuan->perusahaan->alamat_perusahaan }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}</td>
                                            <td>
                                                @if($pengajuan->status_pengajuan === 'pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @elseif($pengajuan->status_pengajuan === 'diterima_perusahaan')
                                                    <span class="badge bg-success">Diterima</span>
                                                @elseif($pengajuan->status_pengajuan === 'ditolak_perusahaan')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @elseif($pengajuan->status_pengajuan === 'dibatalkan')
                                                    <span class="badge bg-secondary">Dibatalkan</span>
                                                @else
                                                    <span class="badge bg-info">{{ ucfirst($pengajuan->status_pengajuan) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($pengajuan->status_pengajuan === 'ditolak_perusahaan' || $pengajuan->status_pengajuan === 'dibatalkan')
                                                    <button class="btn btn-primary btn-sm" wire:click="ajukanKembali({{ $pengajuan->perusahaan->id_perusahaan }})">
                                                        <i class="bi bi-arrow-repeat"></i> Ajukan Kembali
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                                                    Belum ada riwayat pengajuan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $pengajuanData->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi Bootstrap tabs
        document.addEventListener('DOMContentLoaded', function() {
            var triggerTabList = [].slice.call(document.querySelectorAll('#riwayatTabs button'))
            triggerTabList.forEach(function (triggerEl) {
                var tabTrigger = new bootstrap.Tab(triggerEl)
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
    </script>
</div>
