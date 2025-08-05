<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Nilai PKL</h4>
                <p class="text-muted mb-0">Lihat nilai PKL dari prakerin yang telah selesai</p>
            </div>
        </div>

        <!-- Search dan Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <input type="search" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Cari perusahaan...">
                    <select wire:model.live="perPage" class="form-select" style="width: auto;">
                        <option value="5">5 per halaman</option>
                        <option value="10">10 per halaman</option>
                        <option value="20">20 per halaman</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Daftar Prakerin dengan Nilai -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Daftar Prakerin dengan Nilai
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Perusahaan</th>
                                <th>Periode Prakerin</th>
                                <th>Pembimbing Perusahaan</th>
                                <th>Status Nilai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($prakerinSelesai as $index => $prakerin)
                                @php
                                    $penilaian = $prakerin->pembimbingPerusahaan->penilaian()
                                        ->where('nis_siswa', $siswa->nis)
                                        ->first();
                                @endphp
                                <tr>
                                    <td>{{ $prakerinSelesai->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $prakerin->perusahaan->nama_perusahaan }}</div>
                                        <small class="text-muted">{{ $prakerin->perusahaan->alamat_perusahaan }}</small>
                                    </td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') }}</div>
                                        <small class="text-muted">s/d {{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $prakerin->pembimbingPerusahaan->nama ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $prakerin->pembimbingPerusahaan->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($penilaian)
                                            @php
                                                $nilaiRataRata = $penilaian->kompetensi->avg('pivot.nilai');
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">Sudah Dinilai</span>
                                                <span class="fw-bold text-success">{{ number_format($nilaiRataRata, 1) }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-warning">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($penilaian)
                                            <button class="btn btn-primary btn-sm" wire:click="lihatDetailNilai({{ $prakerin->id_prakerin }})">
                                                <i class="bi bi-eye me-1"></i>Lihat Nilai
                                            </button>
                                        @else
                                            <span class="text-muted">Menunggu penilaian</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-star fs-1 d-block mb-2"></i>
                                            Belum ada prakerin selesai dengan nilai
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $prakerinSelesai->links() }}</div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Nilai -->
    @if($showDetailNilai && $selectedPrakerin)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-star-fill text-warning me-2"></i>
                            Detail Nilai PKL
                        </h5>
                        <button type="button" class="btn-close" wire:click="tutupDetailNilai"></button>
                    </div>
                    <div class="modal-body">
                        @php
                            $penilaian = $selectedPrakerin->pembimbingPerusahaan->penilaian()
                                ->where('nis_siswa', $siswa->nis)
                                ->with('kompetensi')
                                ->first();
                        @endphp

                        @if($penilaian)
                            <!-- Informasi Prakerin -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Informasi Prakerin</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Perusahaan:</strong></td>
                                            <td>{{ $selectedPrakerin->perusahaan->nama_perusahaan }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Periode:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($selectedPrakerin->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($selectedPrakerin->tanggal_selesai)->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pembimbing:</strong></td>
                                            <td>{{ $selectedPrakerin->pembimbingPerusahaan->nama ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Ringkasan Nilai</h6>
                                    @php
                                        $nilaiRataRata = $penilaian->kompetensi->avg('pivot.nilai');
                                        $nilaiTertinggi = $penilaian->kompetensi->max('pivot.nilai');
                                        $nilaiTerendah = $penilaian->kompetensi->min('pivot.nilai');
                                    @endphp
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="fw-bold text-primary">{{ number_format($nilaiRataRata, 1) }}</div>
                                                    <small>Rata-rata</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold text-success">{{ $nilaiTertinggi }}</div>
                                                    <small>Tertinggi</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="fw-bold text-warning">{{ $nilaiTerendah }}</div>
                                                    <small>Terendah</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Nilai Kompetensi -->
                            <h6 class="fw-bold mb-3">Detail Nilai Kompetensi</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Kompetensi</th>
                                            <th class="text-center">Nilai</th>
                                            <th class="text-center">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penilaian->kompetensi as $index => $kompetensi)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $kompetensi->nama_kompetensi }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $kompetensi->pivot->nilai >= 85 ? 'success' : ($kompetensi->pivot->nilai >= 75 ? 'warning' : 'danger') }}">
                                                        {{ $kompetensi->pivot->nilai }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($kompetensi->pivot->nilai >= 85)
                                                        <span class="text-success">Sangat Baik</span>
                                                    @elseif($kompetensi->pivot->nilai >= 75)
                                                        <span class="text-warning">Baik</span>
                                                    @else
                                                        <span class="text-danger">Perlu Perbaikan</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Grafik Nilai -->
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Grafik Nilai</h6>
                                <canvas id="nilaiChart" width="400" height="200"></canvas>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-exclamation-triangle text-warning fs-1 d-block mb-3"></i>
                                <h6>Nilai Belum Tersedia</h6>
                                <p class="text-muted">Pembimbing perusahaan belum memberikan penilaian untuk prakerin ini.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="tutupDetailNilai">
                            <i class="bi bi-x-circle me-2"></i>Tutup
                        </button>
                        @if($penilaian)
                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Cetak
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @if($showDetailNilai && $selectedPrakerin)
        <script>
            // Grafik nilai menggunakan Chart.js
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('nilaiChart').getContext('2d');
                const nilaiData = @json($penilaian->kompetensi->pluck('pivot.nilai'));
                const kompetensiLabels = @json($penilaian->kompetensi->pluck('nama_kompetensi'));
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: kompetensiLabels,
                        datasets: [{
                            label: 'Nilai Kompetensi',
                            data: nilaiData,
                            backgroundColor: 'rgba(54, 113, 255, 0.8)',
                            borderColor: 'rgba(54, 113, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</div> 