<div>
    {{-- Style untuk preview gambar dan elemen lainnya --}}
    <style>
        .preview-img { max-height: 150px; width: auto; border-radius: 0.5rem; border: 1px solid #ddd; margin-top: 1rem; }
        .table-img { width: 60px; height: 60px; object-fit: cover; border-radius: 0.5rem; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
    </style>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Header: Kontrol Tabel dan Tombol Tambah --}}
            <div class="d-flex mb-4 justify-content-between align-items-center flex-wrap">
                <div class="d-flex gap-2 mb-2 mb-md-0">
                    <select wire:model.live="perPage" class="form-select" style="width: auto;">
                        <option value="5">5 per halaman</option>
                        <option value="10">10 per halaman</option>
                        <option value="20">20 per halaman</option>
                        <option value="50">50 per halaman</option>
                    </select>
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama, email, atau kontak...">
                </div>
                 <button class="btn btn-primary" wire:click="create">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Perusahaan
                </button>
            </div>

            {{-- Tabel Data Perusahaan --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-nowrap">No</th>
                            <th>Logo</th>
                            <th>Nama Perusahaan</th>
                            <th>Alamat</th>
                            <th class="text-nowrap">Kontak Perusahaan</th>
                            {{-- [PERBAIKAN] Menghapus tag penutup </th> yang berlebih --}}
                            <th>Email</th>
                            <th class="text-center text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perusahaanData as $index => $p)
                            <tr wire:key="{{ $p->id_perusahaan }}">
                                <td>{{ $perusahaanData->firstItem() + $index }}</td>
                                <td class="text-center">
                                    <img src="{{ $p->logo_perusahaan ? Storage::url($p->logo_perusahaan) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($p->nama_perusahaan, 0, 1)) }}" alt="{{ $p->nama_perusahaan }}" class="table-img">
                                </td>
                                <td>{{ $p->nama_perusahaan }}</td>
                                <td>{{ Str::limit($p->alamat_perusahaan, 50) }}</td>
                                <td>{{ $p->kontak_perusahaan }}</td>
                                <td>{{ $p->email_perusahaan }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $p->id_perusahaan }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: {{ $p->id_perusahaan }}, method: 'destroy-perusahaan' })"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- [PERBAIKAN] Colspan diubah dari 6 menjadi 7 agar sesuai dengan jumlah kolom tabel --}}
                            <tr><td colspan="7" class="text-center py-4">Tidak ada data perusahaan yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $perusahaanData->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Tambah/Edit Data --}}
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $perusahaanId ? 'Edit Perusahaan' : 'Tambah Perusahaan Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaan" wire:model.defer="nama_perusahaan">
                            @error('nama_perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email_perusahaan" class="form-label">Email Perusahaan <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email_perusahaan') is-invalid @enderror" id="email_perusahaan" wire:model.defer="email_perusahaan">
                            @error('email_perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kontak_perusahaan" class="form-label">Kontak Perusahaan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('kontak_perusahaan') is-invalid @enderror" id="kontak_perusahaan" wire:model.defer="kontak_perusahaan" placeholder="Contoh: 081234567890">
                            @error('kontak_perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat_perusahaan') is-invalid @enderror" id="alamat_perusahaan" wire:model.defer="alamat_perusahaan" rows="3"></textarea>
                            @error('alamat_perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="logo_perusahaan" class="form-label">Logo Perusahaan @if(!$perusahaanId)<span class="text-danger">*</span>@endif</label>
                            <input type="file" class="form-control @error('logo_perusahaan') is-invalid @enderror" id="logo_perusahaan" wire:model="logo_perusahaan">
                            @error('logo_perusahaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="logo_perusahaan" class="text-muted mt-1 small">Mengunggah...</div>
                            
                            @if ($logo_perusahaan)
                                <img src="{{ $logo_perusahaan->temporaryUrl() }}" class="preview-img" alt="Preview Logo">
                            @elseif ($existingLogo)
                                <img src="{{ $existingLogo }}" class="preview-img" alt="Logo Saat Ini">
                            @endif
                        </div>
                        <div class="modal-footer pb-0">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="store">Simpan</span>
                                <span wire:loading wire:target="store" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span wire:loading wire:target="store">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="{{ !$isModalOpen ? 'display: none;' : '' }}"></div>
    @endif
</div>
