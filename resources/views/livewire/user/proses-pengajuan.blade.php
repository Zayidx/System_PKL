<div>
    {{-- Bagian Informasi Perusahaan (tidak ada perubahan) --}}
    <div class="card mb-4">
        <div class="card-body d-flex align-items-center">
            <img src="{{ $perusahaan->logo_perusahaan ? Storage::url($perusahaan->logo_perusahaan) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($perusahaan->nama_perusahaan, 0, 1)) }}" 
                 alt="Logo {{ $perusahaan->nama_perusahaan }}" 
                 class="me-4 rounded border" 
                 style="width: 80px; height: 80px; object-fit: contain; background-color: #f8f9fa;">
            <div>
                <h4 class="mb-1">{{ $perusahaan->nama_perusahaan }}</h4>
                <div class="text-muted small mb-1"><i class="bi bi-geo-alt-fill me-1"></i>{{ $perusahaan->alamat_perusahaan }}</div>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <span><i class="bi bi-telephone-fill me-1"></i>{{ $perusahaan->kontak_perusahaan ?? 'N/A' }}</span>
                    <span><i class="bi bi-envelope-fill me-1"></i>{{ $perusahaan->email_perusahaan ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Status Pengajuan (jika sudah ada) --}}
    @if($pengajuan)
        <div class="alert alert-info">
            <div class="fw-bold mb-1">Status Pengajuan: 
                <span class="badge
                    @switch($pengajuan->status_pengajuan)
                        @case('pending') bg-warning text-dark @break
                        @case('diterima_admin') bg-info text-dark @break
                        @case('diterima_perusahaan') bg-success @break
                        @case('ditolak_admin') @case('ditolak_perusahaan') bg-danger @break
                        @default bg-secondary @break
                    @endswitch">
                    {{ ucfirst(str_replace('_', ' ', $pengajuan->status_pengajuan)) }}
                </span>
            </div>
            <div><b>Tanggal PKL:</b> {{ $pengajuan->tanggal_mulai ? \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') : '-' }} s/d {{ $pengajuan->tanggal_selesai ? \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') : '-' }}</div>
            <div><b>Link CV:</b> <a href="{{ $pengajuan->link_cv }}" target="_blank" rel="noopener noreferrer">{{ $pengajuan->link_cv }}</a></div>
        </div>
    
    {{-- Bagian Form Pengajuan (jika belum ada) --}}
    @else
        <form wire:submit="konfirmasiPengajuan" class="card p-4 shadow-sm">
            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai PKL</label>
                {{-- PERUBAHAN: Tambahkan .live untuk update real-time --}}
                <input type="date" id="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" wire:model.live="tanggal_mulai">
                @error('tanggal_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai PKL</label>
                {{-- PERUBAHAN: Tambahkan .live untuk update real-time --}}
                <input type="date" id="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" wire:model.live="tanggal_selesai">
                @error('tanggal_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Estimasi Durasi PKL</label>
                {{-- PERUBAHAN: Tambahkan loading indicator --}}
                <div class="d-flex align-items-center">
                    <div class="fw-bold">
                        {{ $durasi_hari }} hari (sekitar {{ $durasi_bulan }} bulan)
                    </div>
                    <div wire:loading wire:target="tanggal_mulai, tanggal_selesai" class="ms-2">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Menghitung...</span>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="link_cv" class="form-label">Link CV (Google Drive, Dropbox, dsb)</label>
                {{-- PERUBAHAN: Tambahkan .live untuk validasi real-time --}}
                <input type="url" id="link_cv" class="form-control @error('link_cv') is-invalid @enderror" wire:model.live="link_cv" placeholder="https://...">
                @error('link_cv') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Ajukan Magang</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
    // Pastikan event listener hanya diinisialisasi sekali
    if (typeof livewireListenersAdded === 'undefined') {
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:ajukan-proses', event => {
                // Logika SweetAlert Anda (tidak ada perubahan)
                const theme = typeof getSwalThemeOptions === 'function' ? getSwalThemeOptions() : {background:'#fff',color:'#212529',confirmButtonColor:'#435ebe',cancelButtonColor:'#e0e0e0'};
                Swal.fire({
                    title: 'Konfirmasi Pengajuan',
                    html: `Anda yakin ingin mengajukan magang ke <strong>${event.nama}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ajukan!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: theme.confirmButtonColor,
                    cancelButtonColor: '#d33',
                    background: theme.background,
                    color: theme.color,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('prosesAjukanMagang');
                    }
                });
            });
        });
        window.livewireListenersAdded = true;
    }
</script>
@endpush
