{{-- Riwayat prakerin dan pengajuan siswa dengan aksi selesai/batalkan/perpanjang. --}}
<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
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
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-primary btn-sm" wire:click="bukaModalPerpanjangan({{ $prakerin->id_prakerin }})" wire:loading.attr="disabled">
                                                            <i class="bi bi-arrow-clockwise"></i> Perpanjang
                                                        </button>
                                                        <button class="btn btn-info btn-sm" wire:click="ajukanKembali({{ $prakerin->perusahaan->id_perusahaan }})">
                                                            <i class="bi bi-arrow-repeat"></i> Ajukan Baru
                                                        </button>
                                                        <a href="{{ route('pengguna.nilai') }}" class="btn btn-warning btn-sm">
                                                            <i class="bi bi-star-fill"></i> Cek Nilai
                                                        </a>
                                                        <button class="btn btn-success btn-sm" 
                                                                wire:click="kirimFormPenilaian({{ $prakerin->id_prakerin }})" 
                                                                wire:loading.attr="disabled"
                                                                onclick="console.log('Tombol Kirim Form Penilaian diklik untuk prakerin ID: {{ $prakerin->id_prakerin }}')">
                                                            <i class="bi bi-envelope"></i> Kirim Form Penilaian
                                                        </button>
                                                    </div>
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

    <!-- Modal Perpanjangan Prakerin -->
    @if($showModalPerpanjangan)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-arrow-clockwise me-2"></i>Perpanjang Prakerin
                    </h5>
                    <button type="button" class="btn-close" wire:click="tutupModalPerpanjangan"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="prosesPerpanjangan">
                        <div class="mb-3">
                            <label class="form-label">Perusahaan</label>
                            <input type="text" class="form-control" value="{{ $perusahaanSelesai->where('id_perusahaan', $selectedPerusahaanId)->first()->nama_perusahaan ?? 'N/A' }}" readonly>
                            <small class="text-muted">Perusahaan yang sudah diselesaikan sebelumnya</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggalMulaiPerpanjangan" class="form-label">Tanggal Mulai Perpanjangan </label>
                                <input type="date" class="form-control @error('tanggalMulaiPerpanjangan') is-invalid @enderror" 
                                       id="tanggalMulaiPerpanjangan" wire:model.defer="tanggalMulaiPerpanjangan">
                                @error('tanggalMulaiPerpanjangan') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggalSelesaiPerpanjangan" class="form-label">Tanggal Selesai Perpanjangan </label>
                                <input type="date" class="form-control @error('tanggalSelesaiPerpanjangan') is-invalid @enderror" 
                                       id="tanggalSelesaiPerpanjangan" wire:model.defer="tanggalSelesaiPerpanjangan">
                                @error('tanggalSelesaiPerpanjangan') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keteranganPerpanjangan" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control @error('keteranganPerpanjangan') is-invalid @enderror" 
                                      id="keteranganPerpanjangan" wire:model.defer="keteranganPerpanjangan" 
                                      rows="3" placeholder="Tambahkan keterangan tentang perpanjangan prakerin..."></textarea>
                            @error('keteranganPerpanjangan') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Informasi:</strong> Perpanjangan prakerin akan menggunakan pembimbing dan perusahaan yang sama dengan prakerin sebelumnya.
                        </div> -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="tutupModalPerpanjangan">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="prosesPerpanjangan" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="prosesPerpanjangan">
                            <i class="bi bi-check-circle me-2"></i>Perpanjang Prakerin
                        </span>
                        <span wire:loading wire:target="prosesPerpanjangan" class="spinner-border spinner-border-sm me-2"></span>
                        Memproses...
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

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