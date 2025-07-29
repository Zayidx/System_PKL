<div>
    <a href="{{ route('staffhubin.master.prakerin.siswa', ['id_kelas' => $siswa->id_kelas]) }}" class="btn btn-link p-2 mb-3 bg-primary text-white text-decoration-none"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa</a>
    
    {{-- Informasi Siswa --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Siswa</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    @if($siswa->user && $siswa->user->foto)
                        <img src="{{ asset('storage/' . $siswa->user->foto) }}" alt="Foto Siswa" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                            <p><strong>Nama:</strong> {{ $siswa->nama_siswa }}</p>
                            <p><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Jurusan:</strong> {{ $siswa->jurusan->nama_jurusan ?? '-' }}</p>
                            <p><strong>Email:</strong> {{ $siswa->user->email ?? '-' }}</p>
                            <p><strong>No. Telepon:</strong> {{ $siswa->no_telepon ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Prakerin --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Daftar Prakerin</h5>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2 mb-3">
                <div class="d-flex gap-2">
                    <select wire:model.live="perPage" class="form-select" style="width: auto;">
                        <option value="5">5 per halaman</option>
                        <option value="10">10 per halaman</option>
                        <option value="20">20 per halaman</option>
                    </select>
                </div>
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control" style="width: 300px;" placeholder="Cari nama perusahaan...">
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Perusahaan</th>
                            <th>Pembimbing Perusahaan</th>
                            <th>Pembimbing Sekolah</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prakerinList as $prakerin)
                            <tr>
                                <td>{{ $prakerin->perusahaan->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $prakerin->pembimbingPerusahaan->nama ?? '-' }}</td>
                                <td>
                                    @if($prakerin->pembimbingSekolah)
                                        {{ $prakerin->pembimbingSekolah->nama_pembimbing_sekolah }}
                                    @else
                                        <span class="text-muted">Belum ditugaskan</span>
                                    @endif
                                </td>
                                <td>{{ $prakerin->tanggal_mulai ? \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') : '-' }}</td>
                                <td>{{ $prakerin->tanggal_selesai ? \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    @if(!$prakerin->pembimbingSekolah)
                                        <button class="btn btn-sm btn-outline-primary" wire:click="setPembimbingSekolah({{ $prakerin->id_prakerin }})" data-bs-toggle="modal" data-bs-target="#assignPembimbingModal">
                                            <i class="bi bi-person-plus"></i> Tugaskan Pembimbing
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                            <i class="bi bi-check-circle"></i> Sudah Ditugaskan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada data prakerin yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $prakerinList->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Assign Pembimbing Sekolah --}}
    <div class="modal fade" id="assignPembimbingModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tugaskan Pembimbing Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pembimbingSekolah" class="form-label">Pilih Pembimbing Sekolah</label>
                        <select wire:model="selectedPembimbingSekolahId" class="form-select" id="pembimbingSekolah">
                            <option value="">Pilih Pembimbing Sekolah</option>
                            @foreach($pembimbingSekolahList as $pembimbing)
                                <option value="{{ $pembimbing->nip_pembimbing_sekolah }}">{{ $pembimbing->nama_pembimbing_sekolah }}</option>
                            @endforeach
                        </select>
                        @error('selectedPembimbingSekolahId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="assignPembimbingSekolah">Tugaskan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('alert', (event) => {
            Swal.fire({
                icon: event.type,
                title: event.title,
                text: event.message,
                timer: 3000,
                showConfirmButton: false
            });
        });
    });
</script>
@endpush 