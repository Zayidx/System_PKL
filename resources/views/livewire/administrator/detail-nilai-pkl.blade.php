{{-- Detail penilaian PKL menampilkan statistik nilai dan kompetensi yang dinilai. --}}
<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="bi bi-clipboard-data me-2"></i>Detail Nilai PKL
            </h1>
            <a href="{{ route('administrator.data.penilaian-pkl') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($penilaian)
            <!-- Informasi Siswa -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-person-badge me-2"></i>Informasi Siswa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>NIS:</strong></td>
                                            <td>{{ $penilaian->siswa->nis ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Siswa:</strong></td>
                                            <td>{{ $penilaian->siswa->nama ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kelas:</strong></td>
                                            <td>{{ $penilaian->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jurusan:</strong></td>
                                            <td>{{ $penilaian->siswa->kelas->jurusan->nama_jurusan_lengkap ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Pembimbing:</strong></td>
                                            <td>{{ $penilaian->pembimbingPerusahaan->nama ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Perusahaan:</strong></td>
                                            <td>{{ $penilaian->pembimbingPerusahaan->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jabatan:</strong></td>
                                            <td>{{ $penilaian->pembimbingPerusahaan->jabatan ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge bg-success">Selesai</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Nilai -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ number_format($nilaiRataRata, 1) }}</h3>
                            <small>Rata-rata</small>
                            <div class="mt-2">
                                <span class="badge bg-light text-dark">
                                    {{ $this->getKeteranganRataRata($nilaiRataRata)['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ $nilaiTertinggi }}</h3>
                            <small>Nilai Tertinggi</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3>{{ $nilaiTerendah }}</h3>
                            <small>Nilai Terendah</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3>{{ $kompetensiWithNilai->count() }}</h3>
                            <small>Jumlah Kompetensi</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Nilai Kompetensi -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-list-check me-2"></i>Detail Nilai Kompetensi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="40%">Kompetensi</th>
                                            <th width="15%" class="text-center">Nilai</th>
                                            <th width="20%" class="text-center">Keterangan</th>
                                            <th width="20%" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kompetensiWithNilai as $index => $kompetensi)
                                            @php
                                                $keterangan = $this->getKeteranganNilai($kompetensi->pivot->nilai);
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $kompetensi->nama_kompetensi }}</td>
                                                <td class="text-center">
                                                    <span class="badge {{ $keterangan['badge'] }} fs-6">
                                                        {{ $kompetensi->pivot->nilai }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="{{ $keterangan['class'] }}">
                                                        {{ $keterangan['text'] }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($kompetensi->pivot->nilai >= 75)
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Lulus
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle me-1"></i>Tidak Lulus
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="bi bi-inbox me-2"></i>Tidak ada data nilai kompetensi
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Nilai -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up me-2"></i>Grafik Nilai Kompetensi
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="nilaiChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button onclick="window.print()" class="btn btn-primary me-2">
                        <i class="bi bi-printer me-2"></i>Cetak Laporan
                    </button>
                    <a href="{{ route('administrator.data.penilaian-pkl') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Penilaian
                    </a>
                </div>
            </div>

        @else
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Data penilaian tidak ditemukan.
            </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($kompetensiWithNilai && $kompetensiWithNilai->count() > 0)
                const ctx = document.getElementById('nilaiChart').getContext('2d');
                const labels = @json($kompetensiWithNilai->pluck('nama_kompetensi'));
                const values = @json($kompetensiWithNilai->pluck('pivot.nilai'));
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nilai Kompetensi',
                            data: values,
                            backgroundColor: values.map(value => {
                                if (value >= 85) return 'rgba(40, 167, 69, 0.8)';
                                if (value >= 75) return 'rgba(255, 193, 7, 0.8)';
                                return 'rgba(220, 53, 69, 0.8)';
                            }),
                            borderColor: values.map(value => {
                                if (value >= 85) return 'rgba(40, 167, 69, 1)';
                                if (value >= 75) return 'rgba(255, 193, 7, 1)';
                                return 'rgba(220, 53, 69, 1)';
                            }),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    stepSize: 10
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            @endif
        });
    </script>
    @endpush
</div> 