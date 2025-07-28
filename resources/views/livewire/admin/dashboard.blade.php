<div>
    <div class="page-heading">
        <h3>Dashboard Analitik</h3>
        <p class="text-subtitle text-muted">Ringkasan data dan statistik penting dalam sistem.</p>
    </div>
    <div class="page-content">
        {{-- Baris untuk Kartu Statistik Pengajuan --}}
        <section class="row">
            <div class="col-12">
                <div class="row">
                    {{-- Kartu Pengajuan Pending --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon orange mb-2">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Pending</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pengajuanPending }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Pengajuan Diterima --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Diterima</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pengajuanDiterima }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Pengajuan Ditolak --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon red mb-2">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Ditolak</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pengajuanDitolak }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Total Pengajuan --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="bi bi-journal-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Pengajuan</h6>
                                        <h6 class="font-extrabold mb-0">{{ $pengajuanCount }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Baris untuk Grafik dan Daftar Terbaru --}}
        <section class="row">
            {{-- Kolom Grafik Tren Pengajuan --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title">Tren Pengajuan (12 Bulan Terakhir)</h4>
                    </div>
                    <div class="card-body">
                        <div style="height: 350px;">
                            <canvas id="pengajuanTrenChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Kolom Distribusi Status & User Terbaru --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title">Distribusi Status</h4>
                    </div>
                    <div class="card-body">
                        <div style="height: 170px; margin-bottom: 2rem;">
                            <canvas id="pengajuanStatusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Pengguna Terbaru</h4>
                    </div>
                    <div class="card-content pb-4">
                        @forelse($latestUser as $user)
                        <div class="recent-message d-flex px-4 py-3">
                            <div class="avatar avatar-lg">
                                <img src="{{ $user->foto ? asset('storage/' . $user->foto) : 'https://placehold.co/100x100/6c757d/white?text=' . strtoupper(substr($user->username, 0, 1)) }}" alt="Avatar">
                            </div>
                            <div class="name ms-4">
                                <h5 class="mb-1">{{ Str::limit($user->username, 15) }}</h5>
                                <h6 class="text-muted mb-0">{{ Str::ucfirst($user->role->name ?? 'N/A') }}</h6>
                            </div>
                        </div>
                        @empty
                        <p class="px-4">Tidak ada pengguna baru.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Baris untuk Daftar Pengajuan Terbaru dan Perusahaan Populer --}}
        <section class="row">
            <div class="col-12 col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Pengajuan Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-lg">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Perusahaan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestPengajuan as $pengajuan)
                                        <tr>
                                            <td class="text-bold-500">{{ $pengajuan->siswa->nama_siswa ?? 'N/A' }}</td>
                                            <td>{{ $pengajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $statusClass = '';
                                                    if ($pengajuan->status_pengajuan == 'pending') $statusClass = 'badge bg-light-warning';
                                                    else if ($pengajuan->status_pengajuan == 'diterima_perusahaan') $statusClass = 'badge bg-light-success';
                                                    else if (in_array($pengajuan->status_pengajuan, ['ditolak_admin', 'ditolak_perusahaan'])) $statusClass = 'badge bg-light-danger';
                                                @endphp
                                                <span class="{{ $statusClass }}">{{ str_replace('_', ' ', Str::ucfirst($pengajuan->status_pengajuan)) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada data pengajuan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Perusahaan Terpopuler</h4>
                    </div>
                    <div class="card-body">
                         <ul class="list-group list-group-flush">
                            @forelse($topPerusahaan as $perusahaan)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-building me-2"></i>{{ $perusahaan->nama_perusahaan }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $perusahaan->pengajuan_count }} pengajuan</span>
                                </li>
                            @empty
                                <li class="list-group-item">Tidak ada data perusahaan.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
{{-- Pastikan Chart.js sudah di-load oleh layout utama Anda --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        // Hancurkan chart lama jika ada untuk mencegah duplikasi
        if (window.trenChart instanceof Chart) window.trenChart.destroy();
        if (window.statusChart instanceof Chart) window.statusChart.destroy();

        // 1. Grafik Tren Pengajuan (Line Chart)
        const trenCtx = document.getElementById('pengajuanTrenChart');
        if (trenCtx) {
            window.trenChart = new Chart(trenCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($pengajuanTren['labels']),
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: @json($pengajuanTren['data']),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Grafik Distribusi Status (Doughnut Chart)
        const statusCtx = document.getElementById('pengajuanStatusChart');
        if (statusCtx) {
            window.statusChart = new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: @json($pengajuanPie['labels']),
                    datasets: [{
                        label: 'Jumlah',
                        data: @json($pengajuanPie['data']),
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.8)', // Orange (Pending)
                            'rgba(75, 192, 192, 0.8)',  // Green (Diterima)
                            'rgba(255, 99, 132, 0.8)'   // Red (Ditolak)
                        ],
                        borderColor: [
                            'rgba(255, 159, 64, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
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
                            labels: {
                                boxWidth: 12,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush