<div>
    <style>
        .preview-img { max-height: 150px; width: auto; border-radius: 0.5rem; border: 1px solid #ddd; margin-top: 1rem; }
        .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
        .modal-body { max-height: 70vh; overflow-y: auto; }
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
                    </select>
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari NIP, nama, atau email...">
                </div>
                 <button class="btn btn-primary" wire:click="create">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Wali Kelas
                </button>
            </div>

            {{-- Tabel Data Wali Kelas --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIP/ID</th>
                            <th>Nama Wali Kelas</th>
                            <th>Email Akun</th>
                            <th>Kelas Diampu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waliKelasData as $index => $wali)
                            <tr wire:key="{{ $wali->nip_wali_kelas }}">
                                <td>{{ $waliKelasData->firstItem() + $index }}</td>
                                <td class="text-center">
                                    <img src="{{ $wali->user && $wali->user->foto ? Storage::url($wali->user->foto) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($wali->nama_wali_kelas, 0, 1)) }}" alt="{{ $wali->nama_wali_kelas }}" class="table-img">
                                </td>
                                <td>{{ $wali->nip_wali_kelas }}</td>
                                <td class="fw-bold">{{ $wali->nama_wali_kelas }}</td>
                                <td>{{ $wali->user->email ?? 'N/A' }}</td>
                                <td>
                                    @if($wali->kelas)
                                        <span class="badge bg-primary">{{ $wali->kelas->nama_kelas }}</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak mengampu</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $wali->nip_wali_kelas }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: {{ $wali->nip_wali_kelas }}, method: 'destroy-wali-kelas' })"
                                            @if($wali->kelas) data-bs-toggle="tooltip" title="Tidak dapat dihapus karena masih mengampu kelas" disabled @endif>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">Tidak ada data wali kelas yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $waliKelasData->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Tambah/Edit Data --}}
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $waliKelasId ? 'Edit Wali Kelas' : 'Tambah Wali Kelas Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nip_wali_kelas" class="form-label">NIP/ID Wali Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nip_wali_kelas') is-invalid @enderror" id="nip_wali_kelas" wire:model.defer="nip_wali_kelas">
                                @error('nip_wali_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_wali_kelas" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_wali_kelas') is-invalid @enderror" id="nama_wali_kelas" wire:model.defer="nama_wali_kelas">
                                @error('nama_wali_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12"><hr class="my-2"></div>
                            <h6 class="mb-3 px-3">Informasi Akun (Role: Wali Kelas)</h6>
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model.defer="email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password @if(!$waliKelasId)<span class="text-danger">*</span>@else<small>(Opsional)</small>@endif</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model.defer="password" placeholder="Isi untuk mengubah">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" wire:model.defer="password_confirmation">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="foto" class="form-label">Foto Profil @if(!$waliKelasId)<span class="text-danger">*</span>@endif</label>
                                <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" wire:model="foto">
                                @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div wire:loading wire:target="foto" class="text-muted mt-1 small">Mengunggah...</div>
                                @if ($foto) <img src="{{ $foto->temporaryUrl() }}" class="preview-img"> @elseif ($existingFoto) <img src="{{ $existingFoto }}" class="preview-img"> @endif
                            </div>
                        </div>
                        <div class="modal-footer pb-0">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="store">Simpan</span>
                                <span wire:loading wire:target="store" class="spinner-border spinner-border-sm"></span>
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
