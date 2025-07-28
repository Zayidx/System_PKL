<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary ">
            <h4 class="mb-0 text-white">Konfirmasi Pengajuan Perusahaan Baru</h4>
        </div>
        <div class="card-body mt-5">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped ">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>Kontak</th>
                            <th>Pengaju (NIS)</th>
                            <th>Catatan Staff</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mitraList as $index => $mitra)
                            <tr>
                                <td>{{ $mitraList->firstItem() + $index }}</td>
                                <td>{{ $mitra->nama_perusahaan }}</td>
                                <td>{{ $mitra->alamat_perusahaan }}</td>
                                <td>{{ $mitra->email_perusahaan ?? '-' }}</td>
                                <td>{{ $mitra->kontak_perusahaan ?? '-' }}</td>
                                <td>{{ $mitra->nis_pengaju }}</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="catatan_staff.{{ $mitra->id }}" placeholder="Catatan (opsional)">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-success btn-sm me-1" wire:click="approve({{ $mitra->id }})">
                                        <i class="bi bi-check-circle"></i> ACC
                                    </button>
                                    <button class="btn btn-danger btn-sm" wire:click="reject({{ $mitra->id }})">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Tidak ada pengajuan perusahaan baru yang menunggu konfirmasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $mitraList->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
