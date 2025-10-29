{{-- Manajemen staf hubin beserta akun dan foto profil. --}}
<div>
    <style>
        .preview-img { max-height: 150px; width: auto; border-radius: 0.5rem; border: 1px solid #ddd; margin-top: 1rem; }
        .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
        .modal-body { max-height: 70vh; overflow-y: auto; }
    </style>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex mb-4 justify-content-between align-items-center flex-wrap">
                <div class="d-flex gap-2 mb-2 mb-md-0">
                    <select wire:model.live="perPage" class="form-select" style="width: auto;">
                        <option value="5">5</option> <option value="10">10</option> <option value="20">20</option>
                    </select>
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari NIP, nama, atau email...">
                </div>
                 <button class="btn btn-primary" wire:click="create"><i class="bi bi-plus-circle me-2"></i>Tambah Staff</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIP/ID</th>
                            <th>Nama Staff</th>
                            <th>Email Akun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($staffData as $index => $s)
                            <tr wire:key="{{ $s->nip_staff }}">
                                <td>{{ $staffData->firstItem() + $index }}</td>
                                <td class="text-center">
                                    <img src="{{ $s->user && $s->user->foto ? Storage::url($s->user->foto) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($s->nama_staff, 0, 1)) }}" alt="{{ $s->nama_staff }}" class="table-img">
                                </td>
                                <td>{{ $s->nip_staff }}</td>
                                <td class="fw-bold">{{ $s->nama_staff }}</td>
                                <td>{{ $s->user->email ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $s->nip_staff }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: {{ $s->nip_staff }}, method: 'destroy-staff-hubin' })"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">Tidak ada data staff hubin yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $staffData->links() }}</div>
        </div>
    </div>

    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $staffId ? 'Edit Staff' : 'Tambah Staff Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nip_staff" class="form-label">NIP/ID Staff <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nip_staff') is-invalid @enderror" id="nip_staff" wire:model.defer="nip_staff">
                                @error('nip_staff') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_staff" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_staff') is-invalid @enderror" id="nama_staff" wire:model.defer="nama_staff">
                                @error('nama_staff') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12"><hr class="my-2"></div>
                            <h6 class="mb-3 px-3">Informasi Akun (Role: Staf Hubin)</h6>
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model.defer="email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password @if(!$staffId)<span class="text-danger">*</span>@else<small>(Opsional)</small>@endif</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model.defer="password" placeholder="Isi untuk mengubah">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" wire:model.defer="password_confirmation">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="foto" class="form-label">Foto Profil @if(!$staffId)<span class="text-danger">*</span>@endif</label>
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