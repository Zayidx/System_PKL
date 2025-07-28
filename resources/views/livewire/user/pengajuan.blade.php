<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary border-bottom d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0 text-white">Daftar Perusahaan untuk Pengajuan Magang</h4>
            <div class="input-group mt-2 mt-md-0" style="width: 300px;">
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari perusahaan...">
            </div>
        </div>
        <div class="card-body py-4">
            <div class="mb-3">
                <a href="{{ route('user.ajukan-perusahaan-baru') }}" class="btn btn-primary">Ajukan Perusahaan Baru</a>
            </div>
            {{-- Grid layout untuk kartu perusahaan --}}
            <div class="row g-4" wire:loading.class.delay="opacity-50">
                @forelse($perusahaanData as $perusahaan)
                    @php
                        // Mengambil data pengajuan untuk perusahaan ini dari koleksi
                        $pengajuan = $pengajuanSiswa->get($perusahaan->id_perusahaan);
                    @endphp
                    <div class="col-lg-6 col-md-12">
                        <div class="card h-100 shadow-sm border">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start">
                                    {{-- Logo Perusahaan --}}
                                    <img src="{{ $perusahaan->logo_perusahaan ? Storage::url($perusahaan->logo_perusahaan) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($perusahaan->nama_perusahaan, 0, 1)) }}" 
                                         alt="Logo {{ $perusahaan->nama_perusahaan }}" 
                                         class="me-4 rounded border" 
                                         style="width: 80px; height: 80px; object-fit: contain; background-color: #f8f9fa;">
                                    
                                    <div class="flex-grow-1">
                                        {{-- Nama dan Alamat --}}
                                        <h5 class="mb-1">{{ $perusahaan->nama_perusahaan }}</h5>
                                        <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill me-1"></i>{{ $perusahaan->alamat_perusahaan }}</p>
                                        
                                        {{-- Kontak dan Email --}}
                                        <div class="d-flex flex-wrap gap-3 small text-muted mb-3">
                                            <span><i class="bi bi-telephone-fill me-1"></i>{{ $perusahaan->kontak_perusahaan ?? 'N/A' }}</span>
                                            <span><i class="bi bi-envelope-fill me-1"></i>{{ $perusahaan->email_perusahaan ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Footer Dinamis Berdasarkan Status --}}
                            @if($pengajuan)
                                @php
                                    $footerClass = '';
                                    $statusText = '';
                                    $statusIcon = '';

                                    switch($pengajuan->status_pengajuan) {
                                        case 'pending':
                                            $footerClass = 'bg-warning text-dark';
                                            $statusIcon = 'bi-clock-history';
                                            $statusText = 'Menunggu Admin';
                                            break;
                                        case 'diterima_admin':
                                            $footerClass = 'bg-info text-dark';
                                            $statusIcon = 'bi-hourglass-split';
                                            $statusText = 'Menunggu Perusahaan';
                                            break;
                                        case 'diterima_perusahaan':
                                            $footerClass = 'bg-success text-white';
                                            $statusIcon = 'bi-check-circle-fill';
                                            $statusText = 'Diterima';
                                            break;
                                        case 'ditolak_admin':
                                        case 'ditolak_perusahaan':
                                            $footerClass = 'bg-danger text-white';
                                            $statusIcon = 'bi-x-octagon-fill';
                                            $statusText = 'Ditolak';
                                            break;
                                        default:
                                            $footerClass = 'bg-secondary text-white';
                                            $statusIcon = 'bi-question-circle';
                                            $statusText = ucfirst(str_replace('_', ' ', $pengajuan->status_pengajuan));
                                            break;
                                    }
                                @endphp
                                <div class="card-footer {{ $footerClass }} d-flex justify-content-between align-items-center">
                                    <div>
                                        <small>Diajukan: {{ $pengajuan->created_at->format('d M Y') }}</small>
                                    </div>
                                    <div class="fw-bold">
                                        <i class="bi {{ $statusIcon }} me-1"></i> {{ $statusText }}
                                    </div>
                                </div>
                            @else
                                {{-- Pada bagian tombol Ajukan Magang --}}
                                @if(!$showModal)
                                    <a href="{{ route('user.pengajuan.proses', ['id_perusahaan' => $perusahaan->id_perusahaan]) }}"
                                       class="card-footer bg-primary text-white text-center fw-bold footer-hover text-decoration-none"
                                       style="cursor: pointer;">
                                        <i class="bi bi-send-fill me-1"></i> Ajukan Magang
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-building-slash fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="mb-1">Perusahaan Tidak Ditemukan</h5>
                            <p class="text-muted">Coba sesuaikan kata kunci pencarian Anda.</p>
                        </div>
                    </div>
                @endforelse
            </div>
             <div class="mt-4">
                {{ $perusahaanData->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Form Kontrak PKL --}}
@if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.4);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Isi Kontrak PKL</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="konfirmasiPengajuanSetelahForm">
                    <div class="modal-body">
                        @if(!$isPerusahaanTerdaftar)
                        <div class="mb-3">
                            <label for="nama_perusahaan_manual" class="form-label">Nama Perusahaan</label>
                            <input type="text" id="nama_perusahaan_manual" class="form-control @error('nama_perusahaan_manual') is-invalid @enderror" wire:model.defer="nama_perusahaan_manual" placeholder="Nama perusahaan">
                            @error('nama_perusahaan_manual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alamat_perusahaan_manual" class="form-label">Alamat Perusahaan</label>
                            <input type="text" id="alamat_perusahaan_manual" class="form-control @error('alamat_perusahaan_manual') is-invalid @enderror" wire:model.defer="alamat_perusahaan_manual" placeholder="Alamat perusahaan">
                            @error('alamat_perusahaan_manual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif
                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai PKL</label>
                            <input type="date" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" wire:model.defer="tanggal_mulai">
                            @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai PKL</label>
                            <input type="date" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" wire:model.defer="tanggal_selesai">
                            @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="link_cv" class="form-label">Link CV (Google Drive, Dropbox, dsb)</label>
                            <input type="url" id="link_cv" class="form-control @error('link_cv') is-invalid @enderror" wire:model.defer="link_cv" placeholder="https://...">
                            @error('link_cv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">Batal</button>
                        <button type="submit" class="btn btn-primary">Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif

{{-- Modal Form Pengajuan Mitra Perusahaan Baru --}}
@if($showModalMitra)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.4);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Perusahaan Baru</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModalMitra', false)"></button>
                </div>
                <form wire:submit.prevent="ajukanMitraBaru">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_mitra" class="form-label">Nama Perusahaan</label>
                            <input type="text" id="nama_mitra" class="form-control @error('nama_mitra') is-invalid @enderror" wire:model.defer="nama_mitra" placeholder="Nama perusahaan">
                            @error('nama_mitra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alamat_mitra" class="form-label">Alamat Perusahaan</label>
                            <input type="text" id="alamat_mitra" class="form-control @error('alamat_mitra') is-invalid @enderror" wire:model.defer="alamat_mitra" placeholder="Alamat perusahaan">
                            @error('alamat_mitra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email_mitra" class="form-label">Email Perusahaan (opsional)</label>
                            <input type="email" id="email_mitra" class="form-control @error('email_mitra') is-invalid @enderror" wire:model.defer="email_mitra" placeholder="Email perusahaan">
                            @error('email_mitra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kontak_mitra" class="form-label">Kontak Perusahaan (opsional)</label>
                            <input type="text" id="kontak_mitra" class="form-control @error('kontak_mitra') is-invalid @enderror" wire:model.defer="kontak_mitra" placeholder="No. telepon/HP">
                            @error('kontak_mitra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModalMitra', false)">Batal</button>
                        <button type="submit" class="btn btn-primary">Ajukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif

@push('styles')
<style>
    .footer-hover:hover {
        filter: brightness(1.1);
        transition: filter .2s ease-in-out;
    }
</style>
@endpush
