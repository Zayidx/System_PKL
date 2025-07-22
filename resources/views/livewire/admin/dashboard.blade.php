<div>
    {{-- Menggunakan class bawaan dari template Anda untuk konsistensi --}}
    <div class="page-heading">
        <h3>Statistik Dashboard</h3>
    </div>
    <div class="page-content">
        {{-- Baris untuk Kartu Statistik Utama --}}
        <section class="row">
            <div class="col-12">
                <div class="row">
                    {{-- Kartu Total User --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card shadow-sm" style="background: var(--bs-card-bg, var(--bs-body-bg));">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total User</h6>
                                        <h6 class="font-extrabold mb-0">{{ $userCount }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Total Siswa --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card shadow-sm" style="background: var(--bs-card-bg, var(--bs-body-bg));">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldUser"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Siswa</h6>
                                        <h6 class="font-extrabold mb-0">{{ $siswaCount }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Total Guru --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card shadow-sm" style="background: var(--bs-card-bg, var(--bs-body-bg));">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldAdd-User"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Guru</h6>
                                        <h6 class="font-extrabold mb-0">{{ $guruCount }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Kartu Total Perusahaan --}}
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card shadow-sm" style="background: var(--bs-card-bg, var(--bs-body-bg));">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon red mb-2">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Perusahaan</h6>
                                        <h6 class="font-extrabold mb-0">{{ $perusahaanCount }}</h6>
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
            {{-- Kolom Grafik --}}
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="card-title">Statistik Visual Keseluruhan</h4>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="statistikChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Kolom Daftar User Terbaru --}}
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm" style="height: calc(100% - 1.5rem);">
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

        {{-- Baris untuk Daftar Siswa dan Perusahaan Terbaru --}}
        <section class="row">
            <div class="col-12 col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Siswa Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($latestSiswa as $siswa)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $siswa->nama_siswa ?? $siswa->user->username ?? '-' }}
                                    <span class="badge" style="background: var(--bs-secondary-bg, #f8f9fa); color: var(--bs-secondary-color, #212529);">{{ $siswa->nis }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">Tidak ada siswa baru.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>Perusahaan Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($latestPerusahaan as $perusahaan)
                                <li class="list-group-item">{{ $perusahaan->nama_perusahaan }}</li>
                            @empty
                                <li class="list-group-item">Tidak ada perusahaan baru.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@push('scripts')
{{-- Pastikan Chart.js sudah di-load oleh layout utama Anda, jika belum, aktifkan baris di bawah --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script>
    document.addEventListener('livewire:navigated', () => {
        // Hancurkan chart lama jika ada untuk mencegah duplikasi saat navigasi
        if (window.myChart instanceof Chart) {
            window.myChart.destroy();
        }

        const ctx = document.getElementById('statistikChart');
        if (!ctx) return; // Hentikan jika elemen canvas tidak ditemukan

        window.myChart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Jumlah',
                    data: @json($chartData['data']),
                    backgroundColor: [
                        'rgba(153, 102, 255, 0.7)', // Purple
                        'rgba(54, 162, 235, 0.7)',  // Blue
                        'rgba(75, 192, 192, 0.7)',  // Green
                        'rgba(255, 99, 132, 0.7)'   // Red
                    ],
                    borderColor: [
                        'rgba(153, 102, 255, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        boxPadding: 3,
                        callbacks: {
                            label: function(context) {
                                return ` Jumlah: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            // Membuat angka di sumbu Y menjadi kelipatan yang rapi
                            stepSize: Math.ceil(Math.max(...@json($chartData['data'])) / 5)
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
