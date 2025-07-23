<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary border-bottom d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0 text-white">Daftar Perusahaan untuk Pengajuan Magang</h4>
            <div class="input-group mt-2 mt-md-0" style="width: 300px;">
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari perusahaan...">
            </div>
        </div>
        <div class="card-body py-4">
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
                                <div wire:click="$dispatch('swal:ajukan', { id: {{ $perusahaan->id_perusahaan }}, nama: '{{ $perusahaan->nama_perusahaan }}' })"
                                     class="card-footer bg-primary text-white text-center fw-bold footer-hover"
                                     style="cursor: pointer;">
                                    <i class="bi bi-send-fill me-1"></i> Ajukan Magang
                                </div>
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

@push('styles')
<style>
    .footer-hover:hover {
        filter: brightness(1.1);
        transition: filter .2s ease-in-out;
    }
</style>
@endpush
