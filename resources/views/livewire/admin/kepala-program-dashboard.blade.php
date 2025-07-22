<div>
    <style>
        .preview-img { max-height: 150px; width: auto; border-radius: 0.5rem; border: 1px solid #ddd; margin-top: 1rem; }
        .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; }
    </style>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex mb-4 justify-content-between align-items-center flex-wrap">
                <div class="d-flex gap-2 mb-2 mb-md-0">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari nama, NIP, atau email...">
                </div>
                 <button class="btn btn-primary" wire:click="create"><i class="bi bi-plus-circle me-2"></i>Tambah Kepala Program</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Email Akun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kaprogData as $index => $k)
                            <tr wire:key="{{ $k->nip_kepala_program }}">
                                <td>{{ $kaprogData->firstItem() + $index }}</td>
                                <td class="text-center"><img src="{{ $k->user && $k->user->foto ? Storage::url($k->user->foto) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($k->nama_kepala_program, 0, 1)) }}" alt="{{ $k->nama_kepala_program }}" class="table-img"></td>
                                <td>{{ $k->nip_kepala_program }}</td>
                                <td class="fw-bold">{{ $k->nama_kepala_program }}</td>
                                <td><span class="badge bg-info">{{ $k->jurusan->nama_jurusan_lengkap ?? 'N/A' }}</span></td>
                                <td>{{ $k->user->email ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $k->nip_kepala_program }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: {{ $k->nip_kepala_program }}, method: 'destroy-kepala-program' })"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">Tidak ada data kepala program.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $kaprogData->links() }}</div>
        </div>
    </div>

    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">{{ $kaprogId ? 'Edit Data' : 'Tambah Data Baru' }}</h5><button type="button" class="btn-close" wire:click="closeModal"></button></div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="nip_kepala_program" class="form-label">NIP <span class="text-danger">*</span></label><input type="text" class="form-control @error('nip_kepala_program') is-invalid @enderror" id="nip_kepala_program" wire:model.defer="nip_kepala_program">@error('nip_kepala_program') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                            <div class="col-md-6 mb-3"><label for="nama_kepala_program" class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input type="text" class="form-control @error('nama_kepala_program') is-invalid @enderror" id="nama_kepala_program" wire:model.defer="nama_kepala_program">@error('nama_kepala_program') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                            <div class="col-12 mb-3"><label for="id_jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label><select class="form-select @error('id_jurusan') is-invalid @enderror" id="id_jurusan" wire:model.defer="id_jurusan"><option value="">Pilih Jurusan</option>@foreach($jurusanOptions as $jurusan)<option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan_lengkap }}</option>@endforeach</select>@error('id_jurusan') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                            <div class="col-12"><hr class="my-2"></div>
                            <h6 class="mb-3 px-3">Informasi Akun (Role: Kepala Program)</h6>
                            <div class="col-12 mb-3"><label for="email" class="form-label">Email <span class="text-danger">*</span></label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model.defer="email">@error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                            <div class="col-md-6 mb-3"><label for="password" class="form-label">Password @if(!$kaprogId)<span class="text-danger">*</span>@else<small>(Opsional)</small>@endif</label><input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model.defer="password" placeholder="Isi untuk mengubah">@error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
                            <div class="col-md-6 mb-3"><label for="password_confirmation" class="form-label">Konfirmasi Password</label><input type="password" class="form-control" id="password_confirmation" wire:model.defer="password_confirmation"></div>
                            <div class="col-12 mb-3"><label for="foto" class="form-label">Foto Profil @if(!$kaprogId)<span class="text-danger">*</span>@endif</label><input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" wire:model="foto">@error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror<div wire:loading wire:target="foto" class="text-muted mt-1 small">Mengunggah...</div>@if ($foto) <img src="{{ $foto->temporaryUrl() }}" class="preview-img"> @elseif ($existingFoto) <img src="{{ $existingFoto }}" class="preview-img"> @endif</div>
                        </div>
                        <div class="modal-footer pb-0"><button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button><button type="submit" class="btn btn-primary" wire:loading.attr="disabled"><span wire:loading.remove wire:target="store">Simpan</span><span wire:loading wire:target="store" class="spinner-border spinner-border-sm"></span></button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="{{ !$isModalOpen ? 'display: none;' : '' }}"></div>
    @endif
</div>
