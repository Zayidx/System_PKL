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
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari NIS, nama, atau email...">
                </div>
                 <button class="btn btn-primary" wire:click="create">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Siswa
                </button>
            </div>

            {{-- Tabel Data Siswa --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Kontak</th>
                            <th>Email</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswaData as $index => $siswa)
                            <tr wire:key="{{ $siswa->nis }}">
                                <td>{{ $siswaData->firstItem() + $index }}</td>
                                <td class="text-center">
                                    @if($siswa->user && $siswa->user->foto)
                                        <img src="{{ Storage::url($siswa->user->foto) }}" alt="{{ $siswa->nama_siswa }}" class="table-img">
                                    @else
                                        <img src="https://placehold.co/100x100/6c757d/white?text={{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}" alt="{{ $siswa->nama_siswa }}" class="table-img">
                                    @endif
                                </td>
                                <td>{{ $siswa->nis }}</td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td>{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                <td>{{ $siswa->jurusan->nama_jurusan_singkat ?? 'N/A' }}</td>
                                <td>{{ $siswa->kontak_siswa }}</td>
                                <td>{{ $siswa->user->email ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit('{{ $siswa->nis }}')"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: '{{ $siswa->nis }}', method: 'destroy-siswa' })"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4">Tidak ada data siswa yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $siswaData->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Tambah/Edit Data --}}
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $siswaNis ? 'Edit Siswa' : 'Tambah Siswa Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror" id="nis" wire:model.defer="nis">
                                    @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_siswa" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_siswa') is-invalid @enderror" id="nama_siswa" wire:model.defer="nama_siswa">
                                    @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" wire:model.defer="tempat_lahir">
                                    @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" wire:model.defer="tanggal_lahir">
                                    @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_jurusan') is-invalid @enderror" id="id_jurusan" wire:model.defer="id_jurusan">
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($jurusanOptions as $jurusan)
                                            <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan_lengkap }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_jurusan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-select @error('id_kelas') is-invalid @enderror" id="id_kelas" wire:model.defer="id_kelas">
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelasOptions as $kelas)
                                            <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12"><hr class="my-3"></div>
                            <h6 class="mb-3">Informasi Akun</h6>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model.defer="email">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kontak_siswa" class="form-label">Kontak (No. HP/WA) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kontak_siswa') is-invalid @enderror" id="kontak_siswa" wire:model.defer="kontak_siswa">
                                    @error('kontak_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password @if(!$siswaNis)<span class="text-danger">*</span>@else<small>(Opsional)</small>@endif</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model.defer="password" placeholder="Isi untuk mengubah">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" wire:model.defer="password_confirmation">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Profil @if(!$siswaNis)<span class="text-danger">*</span>@endif</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" wire:model="foto">
                                    @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div wire:loading wire:target="foto" class="text-muted mt-1 small">Mengunggah...</div>
                                    
                                    @if ($foto)
                                        <img src="{{ $foto->temporaryUrl() }}" class="preview-img" alt="Preview Foto">
                                    @elseif ($existingFoto)
                                        <img src="{{ $existingFoto }}" class="preview-img" alt="Foto Saat Ini">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer pb-0">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="store">Simpan</span>
                                <span wire:loading wire:target="store" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
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
