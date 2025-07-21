<div>
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
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari jurusan...">
                </div>
                 <button class="btn btn-primary" wire:click="create">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Jurusan
                </button>
            </div>

            {{-- Tabel Data Jurusan --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Jurusan</th>
                            <th>Singkatan</th>
                            <th>Kepala Program</th>
                            <th class="text-center">Jumlah Kelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jurusanData as $index => $jurusan)
                            <tr wire:key="{{ $jurusan->id_jurusan }}">
                                <td>{{ $jurusanData->firstItem() + $index }}</td>
                                <td class="fw-bold">{{ $jurusan->nama_jurusan_lengkap }}</td>
                                <td>{{ $jurusan->nama_jurusan_singkat }}</td>
                                <td>{{ $jurusan->kepalaProgram->nama_kepala_program ?? 'N/A' }}</td>
                                <td class="text-center">{{ $jurusan->kelas_count }}</td>
                                <td class="text-center">{{ $jurusan->siswa_count }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $jurusan->id_jurusan }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: {{ $jurusan->id_jurusan }}, method: 'destroy-jurusan' })"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">Tidak ada data jurusan yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $jurusanData->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Tambah/Edit Data --}}
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $jurusanId ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label for="nama_jurusan_lengkap" class="form-label">Nama Jurusan (Lengkap) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_jurusan_lengkap') is-invalid @enderror" id="nama_jurusan_lengkap" wire:model.defer="nama_jurusan_lengkap" placeholder="Contoh: Rekayasa Perangkat Lunak">
                            @error('nama_jurusan_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nama_jurusan_singkat" class="form-label">Nama Jurusan (Singkat) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_jurusan_singkat') is-invalid @enderror" id="nama_jurusan_singkat" wire:model.defer="nama_jurusan_singkat" placeholder="Contoh: RPL">
                            @error('nama_jurusan_singkat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="kepala_program" class="form-label">Kepala Program <small>(Opsional)</small></label>
                            <select class="form-select @error('kepala_program') is-invalid @enderror" id="kepala_program" wire:model.defer="kepala_program">
                                <option value="">Pilih Kepala Program</option>
                                @foreach($kepalaProgramOptions as $kaprog)
                                    <option value="{{ $kaprog->nip_kepala_program }}">{{ $kaprog->nama_kepala_program }}</option>
                                @endforeach
                            </select>
                            @error('kepala_program') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
