<div class="container-fluid py-2" x-data="dashboardChart()" wire:poll.15s>
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Dashboard Staff Hubin</h4>
                    <p class="text-muted mb-0">Hari ini, {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Tampilkan pesan error jika ada -->
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-sm py-1 px-2 mb-0 me-2">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Tombol Export Prakerin dengan Dropdown Periode -->
                    <!-- Tombol Export Prakerin dengan Dropdown Periode -->
                    <div class="dropdown">
                        <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="exportPrakerinDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Prakerin
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportPrakerinDropdown">
                            <li>
                                <button class="dropdown-item" wire:click="exportExcelPrakerin('all')" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="exportExcelPrakerin('all')">
                                        <i class="bi bi-clock-history me-1"></i> Semua Data
                                    </span>
                                    <span wire:loading wire:target="exportExcelPrakerin('all')">
                                        <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                    </span>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" wire:click="exportExcelPrakerin('today')" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="exportExcelPrakerin('today')">
                                        <i class="bi bi-calendar-day me-1"></i> Hari Ini
                                    </span>
                                    <span wire:loading wire:target="exportExcelPrakerin('today')">
                                        <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                    </span>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" wire:click="exportExcelPrakerin('3days')" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="exportExcelPrakerin('3days')">
                                        <i class="bi bi-calendar3 me-1"></i> 3 Hari Terakhir
                                    </span>
                                    <span wire:loading wire:target="exportExcelPrakerin('3days')">
                                        <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                    </span>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" wire:click="exportExcelPrakerin('7days')" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="exportExcelPrakerin('7days')">
                                        <i class="bi bi-calendar-week me-1"></i> 7 Hari Terakhir
                                    </span>
                                    <span wire:loading wire:target="exportExcelPrakerin('7days')">
                                        <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                    </span>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" wire:click="exportExcelPrakerin('1month')" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="exportExcelPrakerin('1month')">
                                        <i class="bi bi-calendar-month me-1"></i> 1 Bulan Terakhir
                                    </span>
                                    <span wire:loading wire:target="exportExcelPrakerin('1month')">
                                        <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                    </span>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" wire:click="exportExcelPrakerin('1year')" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="exportExcelPrakerin('1year')">
                                        <i class="bi bi-calendar-check me-1"></i> 1 Tahun Terakhir
                                    </span>
                                    <span wire:loading wire:target="exportExcelPrakerin('1year')">
                                        <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                    </span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <div class="dropdown-item">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="date" class="form-control form-control-sm" wire:model="startDate" placeholder="Tanggal Awal">
                                            <span>s/d</span>
                                            <input type="date" class="form-control form-control-sm" wire:model="endDate" placeholder="Tanggal Akhir">
                                        </div>
                                        <button class="btn btn-primary btn-sm w-100" wire:click="exportExcelPrakerin('custom')" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="exportExcelPrakerin('custom')">
                                                Export Periode
                                            </span>
                                            <span wire:loading wire:target="exportExcelPrakerin('custom')">
                                                <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Tombol Export Perusahaan -->
                    <button class="btn btn-info btn-sm" type="button" wire:click="exportExcelPerusahaan" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="exportExcelPerusahaan">
                            <i class="bi bi-building me-1"></i> Export Perusahaan
                        </span>
                        <span wire:loading wire:target="exportExcelPerusahaan">
                            <span class="spinner-border spinner-border-sm" role="status"></span> Exporting...
                        </span>
                    </button>
                    <div class="spinner-border spinner-border-sm text-primary" wire:loading.delay role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-start border-primary border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fs-4 fw-bold text-primary">{{ $statPengajuan }}</div>
                        <div class="text-muted small">Total Pengajuan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-start border-warning border-4 card-hover" wire:click="filterByStatus('pending')" style="cursor: pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fs-4 fw-bold text-warning">{{ $statPending }}</div>
                        <div class="text-muted small">Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-start border-success border-4 card-hover" wire:click="filterByStatus('diterima_perusahaan')" style="cursor: pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fs-4 fw-bold text-success">{{ $statDiterima }}</div>
                        <div class="text-muted small">Diterima</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-start border-danger border-4 card-hover" wire:click="filterByStatus('ditolak')" style="cursor: pointer;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fs-4 fw-bold text-danger">{{ $statDitolak }}</div>
                        <div class="text-muted small">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-start border-info border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fs-4 fw-bold text-info">{{ $statPerusahaan }}</div>
                        <div class="text-muted small">Perusahaan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100 shadow-sm border-start border-secondary border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="fs-4 fw-bold text-secondary">{{ $statSiswa }}</div>
                        <div class="text-muted small">Siswa</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6 col-xl-4">
            <div class="card shadow-sm h-100">
               <div class="card-header">
                    <h5 class="mb-0 card-title"><i class="bi bi-pie-chart-fill me-2"></i>Status Pengajuan</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="statusChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title"><i class="bi bi-calendar-day me-2"></i>Pengajuan Siswa Hari Ini</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Perusahaan</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayPengajuan as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-content bg-primary rounded-circle">{{ substr($p->siswa->nama_siswa ?? 'S', 0, 1) }}</span>
                                            </div>
                                            <div>{{ $p->siswa->nama_siswa ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $p->perusahaan->nama_perusahaan ?? '-' }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $this->getStatusClass($p->status_pengajuan) }}">
                                            {{ ucfirst(str_replace('_',' ',$p->status_pengajuan)) }}
                                        </span>
                                    </td>
                                    <td>{{ $p->created_at->format('H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('staffhubin.master.pengajuan.status', ['nis' => $p->siswa->nis ?? '']) }}" class="btn btn-sm btn-outline-primary icon" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-check2-circle fs-3 text-success"></i>
                                        <p class="mb-0 mt-2">Tidak ada pengajuan baru hari ini. Kerja bagus!</p>
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

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <h5 class="mb-2 mb-md-0 card-title"><i class="bi bi-clock-history me-2"></i>Histori Pengajuan Terbaru</h5>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" class="form-control form-control-sm" placeholder="Cari nama siswa..." wire:model.live.debounce.300ms="search">
                            <select class="form-select form-select-sm" wire:model.live="filterTanggal">
                                <option value="">Semua History</option>
                                <option value="1_hari">Hari ini</option>
                                <option value="3_hari">3 Hari Terakhir</option>
                                <option value="7_hari">7 Hari Terakhir</option>
                                <option value="30_hari">30 Hari Terakhir</option>
                            </select>
                            <select class="form-select form-select-sm" wire:model.live="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="diterima_perusahaan">Diterima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                            @if($filterStatus || $search || $filterTanggal)
                            <button class="btn btn-sm btn-outline-secondary icon" wire:click="clearFilter" title="Hapus Filter">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:loading.delay class="text-center w-100 py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="table-responsive" wire:loading.remove>
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Perusahaan</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyPengajuan as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-content bg-info rounded-circle">{{ substr($p->siswa->nama_siswa ?? 'S', 0, 1) }}</span>
                                            </div>
                                            <div>{{ $p->siswa->nama_siswa ?? '-' }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $p->perusahaan->nama_perusahaan ?? '-' }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $this->getStatusClass($p->status_pengajuan) }}">
                                            {{ ucfirst(str_replace('_',' ',$p->status_pengajuan)) }}
                                        </span>
                                    </td>
                                    <td>{{ $p->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('staffhubin.master.pengajuan.status', ['nis' => $p->siswa->nis ?? '']) }}" class="btn btn-sm btn-outline-primary icon" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-search fs-3 text-muted"></i>
                                        <p class="mb-0 mt-2">Data tidak ditemukan.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0 card-title"><i class="bi bi-person-x-fill me-2 text-danger"></i>Siswa Belum Mengajukan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                             <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswaTanpaPengajuan as $siswa)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-content bg-secondary rounded-circle">{{ substr($siswa->nama_siswa ?? 'S', 0, 1) }}</span>
                                            </div>
                                            <div>{{ $siswa->nama_siswa }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <i class="bi bi-emoji-sunglasses fs-3 text-success"></i>
                                        <p class="mb-0 mt-2">Semua siswa sudah mengajukan. Hebat!</p>
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
</div>

@push('scripts')
{{-- Pastikan Anda sudah meng-import Chart.js di layout utama Anda --}}
{{-- Jika belum, Anda bisa menggunakan CDN seperti ini: --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function dashboardChart() {
        return {
            chartInstance: null,
            init() {
                // Data untuk chart diambil dari property public Livewire
                const statusData = @json($this->chartData);

                const ctx = document.getElementById('statusChart').getContext('2d');
                
                // Hancurkan chart lama jika ada untuk mencegah duplikasi
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                this.chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{
                            label: 'Jumlah Pengajuan',
                            data: Object.values(statusData),
                            backgroundColor: [
                                'rgba(255, 193, 7, 0.8)',   // Warning (Pending)
                                'rgba(25, 135, 84, 0.8)',  // Success (Diterima)
                                'rgba(220, 53, 69, 0.8)',  // Danger (Ditolak)
                            ],
                            borderColor: [
                                'rgba(255, 193, 7, 1)',
                                'rgba(25, 135, 84, 1)',
                                'rgba(220, 53, 69, 1)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed;
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }

    // Inisialisasi chart saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('statusChart')) {
            dashboardChart().init();
        }
    });

    // Inisialisasi ulang chart saat Livewire selesai melakukan navigasi (untuk SPA)
    document.addEventListener('livewire:navigated', () => {
        if (document.getElementById('statusChart')) {
            dashboardChart().init();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,.1) !important;
        transition: all 0.2s ease-in-out;
    }
    .icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush
