<div>
    <div class="page-heading">
        <h3>Proses Magang</h3>
    </div>

    {{-- Informasi Siswa --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informasi Siswa</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    @if(auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Foto Siswa" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                            <i class="bi bi-person-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>NIS:</strong> {{ auth()->user()->siswa->nis }}</p>
                            <p><strong>Nama:</strong> {{ auth()->user()->siswa->nama_siswa }}</p>
                            <p><strong>Kelas:</strong> {{ auth()->user()->siswa->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Jurusan:</strong> {{ auth()->user()->siswa->jurusan->nama_jurusan_lengkap ?? '-' }}</p>
                            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                            <p><strong>No. Telepon:</strong> {{ auth()->user()->siswa->kontak_siswa ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Prakerin --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Daftar Prakerin/Magang</h5>
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
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prakerinList as $prakerin)
                            <tr>
                                <td>{{ $prakerin->perusahaan->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $prakerin->pembimbingPerusahaan->nama ?? '-' }}</td>
                                <td>{{ $prakerin->pembimbingSekolah->nama_pembimbing_sekolah ?? '-' }}</td>
                                <td>{{ $prakerin->tanggal_mulai ? \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') : '-' }}</td>
                                <td>{{ $prakerin->tanggal_selesai ? \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($prakerin->status_prakerin === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($prakerin->status_prakerin === 'selesai')
                                        <span class="badge bg-secondary">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($prakerin->status_prakerin === 'selesai' && $prakerin->tanggal_selesai < now())
                                        <button class="btn btn-sm btn-outline-primary" wire:click="showPerpanjanganModal({{ $prakerin->id_prakerin }})">
                                            <i class="bi bi-arrow-repeat"></i> Perpanjang
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada data prakerin yang ditemukan.</td>
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

    {{-- Modal Perpanjangan Prakerin --}}
    @if($showModalPerpanjangan)
    <div class="modal fade show" style="display: block;" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perpanjang Prakerin</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModalPerpanjangan', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="selectedPerusahaanId" class="form-label">Pilih Perusahaan</label>
                        <select wire:model="selectedPerusahaanId" class="form-select" id="selectedPerusahaanId">
                            <option value="">Pilih Perusahaan</option>
                            @foreach($perusahaanSelesai as $perusahaan)
                                <option value="{{ $perusahaan->id_perusahaan }}">{{ $perusahaan->nama_perusahaan }}</option>
                            @endforeach
                        </select>
                        @error('selectedPerusahaanId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tanggalMulaiBaru" class="form-label">Tanggal Mulai Baru</label>
                        <input type="date" wire:model="tanggalMulaiBaru" class="form-control" id="tanggalMulaiBaru">
                        @error('tanggalMulaiBaru') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tanggalSelesaiBaru" class="form-label">Tanggal Selesai Baru</label>
                        <input type="date" wire:model="tanggalSelesaiBaru" class="form-control" id="tanggalSelesaiBaru">
                        @error('tanggalSelesaiBaru') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showModalPerpanjangan', false)">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="perpanjangPrakerin">Perpanjang</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
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
