<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Setting Pembimbing Sekolah</h4>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="selectedKelas" class="form-label">Filter Kelas</label>
                    <select wire:model.live="selectedKelas" id="selectedKelas" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id_kelas }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="selectedJurusan" class="form-label">Filter Jurusan</label>
                    <select wire:model.live="selectedJurusan" id="selectedJurusan" class="form-select">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="selectedPembimbing" class="form-label">Pembimbing untuk Bulk Assign</label>
                    <select wire:model="selectedPembimbing" id="selectedPembimbing" class="form-select">
                        <option value="">Pilih Pembimbing</option>
                        @foreach($pembimbingList as $pembimbing)
                            <option value="{{ $pembimbing->nip_pembimbing_sekolah }}">{{ $pembimbing->nama_pembimbing_sekolah }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Bulk Action -->
            @if($siswaList && $siswaList->count() > 0)
            <div class="row mb-3">
                <div class="col-12">
                    <button wire:click="bulkAssignPembimbing" class="btn btn-success" 
                            @if(!$selectedPembimbing) disabled @endif>
                        <i class="bi bi-people-fill me-2"></i>
                        Assign Pembimbing ke Semua Siswa
                    </button>
                </div>
            </div>
            @endif

            <!-- Siswa List -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Pembimbing Sekolah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($siswaList && $siswaList->count() > 0)
                            @foreach($siswaList as $index => $siswa)
                                <tr wire:key="{{ $siswa->nis }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $siswa->nis }}</td>
                                    <td>{{ $siswa->nama_siswa }}</td>
                                    <td>{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                    <td>{{ $siswa->jurusan->nama_jurusan_singkat ?? 'N/A' }}</td>
                                    <td>
                                        @if($siswa->pembimbingSekolah)
                                            <span class="badge bg-success">{{ $siswa->pembimbingSekolah->nama_pembimbing_sekolah }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Ditugaskan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if($siswa->pembimbingSekolah)
                                                <button wire:click="removePembimbing('{{ $siswa->nis }}')" 
                                                        class="btn btn-danger btn-sm"
                                                        wire:confirm="Yakin ingin menghapus pembimbing sekolah untuk siswa ini?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @else
                                                <div class="dropdown">
                                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-person-plus"></i> Assign
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach($pembimbingList as $pembimbing)
                                                            <li>
                                                                <a class="dropdown-item" href="#" 
                                                                   wire:click="assignPembimbing('{{ $siswa->nis }}', '{{ $pembimbing->nip_pembimbing_sekolah }}')">
                                                                    {{ $pembimbing->nama_pembimbing_sekolah }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="mb-0 text-muted">Tidak ada data siswa yang ditemukan.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            @if($siswaList && $siswaList->count() > 0)
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Ringkasan</h6>
                            <p class="mb-1">Total Siswa: <strong>{{ $siswaList->count() }}</strong></p>
                            <p class="mb-1">Sudah Ditugaskan: <strong>{{ $siswaList->whereNotNull('nip_pembimbing_sekolah')->count() }}</strong></p>
                            <p class="mb-0">Belum Ditugaskan: <strong>{{ $siswaList->whereNull('nip_pembimbing_sekolah')->count() }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>