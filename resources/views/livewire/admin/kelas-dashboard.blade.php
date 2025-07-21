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
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari kelas, jurusan, wali kelas...">
                </div>
                 <button class="btn btn-primary" wire:click="create">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Kelas
                </button>
            </div>

            {{-- Tabel Data Kelas --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Jurusan</th>
                            <th>Angkatan</th>
                            <th>Wali Kelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kelasData as $index => $kelas)
                            <tr wire:key="{{ $kelas->id_kelas }}">
                                <td>{{ $kelasData->firstItem() + $index }}</td>
                                <td class="fw-bold">{{ $kelas->nama_kelas }}</td>
                                <td>{{ $kelas->tingkat_kelas }}</td>
                                <td>{{ $kelas->jurusan->nama_jurusan_lengkap ?? 'N/A' }}</td>
                                <td>{{ $kelas->angkatan->tahun ?? 'N/A' }}</td>
                                <td>{{ $kelas->waliKelas->nama_wali_kelas ?? 'N/A' }}</td>
                                <td class="text-center">{{ $kelas->siswa_count }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $kelas->id_kelas }})"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-danger btn-sm" wire:click="$dispatch('swal:confirm', { id: {{ $kelas->id_kelas }}, method: 'destroy-kelas' })"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-4">Tidak ada data kelas yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $kelasData->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Tambah/Edit Data --}}
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $kelasId ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" id="nama_kelas" wire:model.defer="nama_kelas" placeholder="Contoh: XII RPL 1">
                                @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tingkat_kelas" class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tingkat_kelas') is-invalid @enderror" id="tingkat_kelas" wire:model.defer="tingkat_kelas" placeholder="Contoh: XII">
                                @error('tingkat_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
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
                        <div class="mb-3">
                            <label for="id_angkatan" class="form-label">Angkatan <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_angkatan') is-invalid @enderror" id="id_angkatan" wire:model.defer="id_angkatan">
                                <option value="">Pilih Angkatan</option>
                                @foreach($angkatanOptions as $angkatan)
                                    <option value="{{ $angkatan->id_angkatan }}">{{ $angkatan->tahun }}</option>
                                @endforeach
                            </select>
                            @error('id_angkatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nip_wali_kelas" class="form-label">Wali Kelas <span class="text-danger">*</span></label>
                            <select class="form-select @error('nip_wali_kelas') is-invalid @enderror" id="nip_wali_kelas" wire:model.defer="nip_wali_kelas">
                                <option value="">Pilih Wali Kelas</option>
                                @foreach($waliKelasOptions as $wali)
                                    <option value="{{ $wali->nip_wali_kelas }}">{{ $wali->nama_wali_kelas }}</option>
                                @endforeach
                            </select>
                            @error('nip_wali_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
