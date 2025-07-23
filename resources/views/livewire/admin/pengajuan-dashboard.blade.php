<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary border-bottom d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="mb-0 text-white">Pilih Kelas untuk Monitoring</h4>
            <div class="input-group mt-2 mt-md-0" style="width: 300px;">
            
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control primar" placeholder="Cari nama kelas..." aria-describedby="search-icon">
            </div>
        </div>
        <div class="card-body py-4">
            <div wire:loading.delay.class="opacity-50 transition-opacity" class="row g-4">
                @forelse($kelasList as $kelas)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <a href="{{ route('staffhubin.master.pengajuan.siswa', ['id_kelas' => $kelas->id_kelas]) }}" class="text-decoration-none text-dark">
                            <div class="card h-100 card-hover-new shadow-sm border-light-subtle">
                                <div class="card-body text-center d-flex flex-column justify-content-center p-4">
                                    
                                    <h5 class="card-title mb-1">{{ $kelas->nama_kelas }}</h5>
                                    <p class="text-muted small mb-2">Tingkat {{ $kelas->tingkat_kelas }}</p>
                                    <span class="badge rounded-pill bg-primary-soft align-self-center">
                                        <i class="bi bi-people-fill me-1"></i>
                                        {{ $kelas->siswa_count }} Siswa
                                    </span>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-center pb-3">
                                    <span class="text-primary small fw-bold">Lihat Detail <i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                         <div class="text-center py-5">
                            <div wire:loading.remove>
                                <i class="bi bi-search-heart fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="mb-1">Kelas Tidak Ditemukan</h5>
                                <p class="text-muted">Coba sesuaikan kata kunci pencarian Anda.</p>
                            </div>
                            {{-- Tampilan saat loading --}}
                            <div wire:loading>
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Memuat...</span>
                                </div>
                                <p class="mt-2 text-muted">Mencari data kelas...</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-hover-new {
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        border: 1px solid var(--bs-card-border-color);
    }
    .card-hover-new:hover {
        transform: translateY(-6px);
        box-shadow: 0 1rem 1.5rem rgba(0,0,0,.1)!important;
        border-color: var(--bs-primary);
    }
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .bg-primary-soft {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        color: var(--bs-primary);
    }
</style>
@endpush
