<?php

namespace App\Livewire\StaffHubin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Pengajuan;
use App\Models\Perusahaan;
use App\Models\Siswa;
use App\Models\Prakerin;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

#[Layout("components.layouts.layout-staff-hubin-dashboard")]
#[Title('Dashboard Staff Hubin')]
class Dashboard extends Component
{
    public $statPengajuan;
    public $statPending;
    public $statDiterima;
    public $statDitolak;
    public $statPerusahaan;
    public $statSiswa;

    public $todayPengajuan;
    public $historyPengajuan;
    public $siswaTanpaPengajuan;
    public $chartData;
    public $search = '';
    public $filterStatus = '';
    public $filterTanggal = '';
    
    // Properti untuk tanggal custom
    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->calculateStats();

        $this->todayPengajuan = Pengajuan::with(['siswa', 'perusahaan'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('created_at')
            ->get();

        // Data untuk chart
        $this->chartData = [
            'Pending' => $this->statPending,
            'Diterima' => $this->statDiterima,
            'Ditolak' => $this->statDitolak
        ];

        // Siswa yang belum mengajukan
        $this->siswaTanpaPengajuan = Siswa::whereDoesntHave('pengajuan')
            ->with(['kelas'])
            ->take(10)
            ->get();
    }
    
    private function calculateStats()
    {
        $this->statPengajuan = Pengajuan::count();
        $this->statPending = Pengajuan::where('status_pengajuan', 'pending')->count();
        $this->statDiterima = Pengajuan::where('status_pengajuan', 'diterima_perusahaan')->count();
        $this->statDitolak = Pengajuan::whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan'])->count();
        $this->statPerusahaan = Perusahaan::count();
        $this->statSiswa = Siswa::count();

        $this->chartData = [
            'Pending' => $this->statPending,
            'Diterima' => $this->statDiterima,
            'Ditolak' => $this->statDitolak,
        ];
    }

    public function filterByStatus($status)
    {
        $this->filterStatus = $status;
    }

    public function clearFilter()
    {
        $this->reset(['search', 'filterStatus', 'filterTanggal']);
    }

    public function getHistoryPengajuan()
    {
        $query = Pengajuan::with(['siswa', 'perusahaan'])
            ->orderByDesc('created_at');

        $query->when($this->search, function ($q) {
            $q->whereHas('siswa', function ($subq) {
                $subq->where('nama_siswa', 'like', '%' . $this->search . '%');
            });
        });

        $query->when($this->filterStatus, function ($q) {
            if ($this->filterStatus === 'ditolak') {
                $q->whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan']);
            } else {
                $q->where('status_pengajuan', $this->filterStatus);
            }
        });

        $query->when($this->filterTanggal, function ($q) {
            $days = match($this->filterTanggal) {
                '1_hari' => 1,
                '3_hari' => 3,
                '7_hari' => 7,
                '30_hari' => 30,
                default => 0,
            };

            if ($days > 0) {
                $q->where('created_at', '>=', Carbon::now()->subDays($days));
            }
        });

        return $query->take(20)->get();
    }

    public function getStatusClass($status)
    {
        return match($status) {
            'pending' => 'bg-light-warning text-warning',
            'diterima_perusahaan' => 'bg-light-success text-success',
            'ditolak_perusahaan', 'ditolak_sekolah' => 'bg-light-danger text-danger',
            default => 'bg-light-secondary text-secondary'
        };
    }

    public function exportExcelPrakerin($period = 'all')
    {
        // Validasi tanggal untuk periode custom
        if ($period === 'custom') {
            if (empty($this->startDate) || empty($this->endDate)) {
                session()->flash('error', 'Tanggal awal dan akhir harus diisi untuk export periode kustom.');
                return;
            }
            
            if (Carbon::parse($this->startDate)->greaterThan(Carbon::parse($this->endDate))) {
                session()->flash('error', 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.');
                return;
            }
        }

        // Buat query dasar untuk siswa dengan informasi prakerin lengkap
        $query = Siswa::with(['kelas', 'prakerin.perusahaan', 'prakerin.pembimbingSekolah', 'prakerin.pembimbingPerusahaan'])
            ->whereHas('prakerin')
            ->orderBy('nama_siswa');

        // Terapkan filter berdasarkan periode menggunakan tanggal mulai prakerin
        switch ($period) {
            case 'today':
                $query->whereHas('prakerin', function ($q) {
                    $q->whereDate('tanggal_mulai', Carbon::today());
                });
                break;
            case '3days':
                $query->whereHas('prakerin', function ($q) {
                    $q->where('tanggal_mulai', '>=', Carbon::now()->subDays(3));
                });
                break;
            case '7days':
                $query->whereHas('prakerin', function ($q) {
                    $q->where('tanggal_mulai', '>=', Carbon::now()->subDays(7));
                });
                break;
            case '1month':
                $query->whereHas('prakerin', function ($q) {
                    $q->where('tanggal_mulai', '>=', Carbon::now()->subMonth());
                });
                break;
            case '1year':
                $query->whereHas('prakerin', function ($q) {
                    $q->where('tanggal_mulai', '>=', Carbon::now()->subYear());
                });
                break;
            case 'custom':
                // Filter berdasarkan tanggal custom
                if (!empty($this->startDate) && !empty($this->endDate)) {
                    $query->whereHas('prakerin', function ($q) {
                        $q->whereBetween('tanggal_mulai', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()]);
                    });
                }
                break;
            case 'all':
            default:
                // Tidak perlu filter tambahan
                break;
        }

        // Ambil data siswa
        $siswaData = $query->get();

        // Format data untuk Excel
        $data = [];
        
        // Header dasar
        $headers = [
            'No',
            'NIS',
            'Nama Siswa',
            'Kelas'
        ];

        // Tentukan jumlah maksimum prakerin per siswa untuk menentukan jumlah kolom
        $maxPrakerin = 1;
        foreach ($siswaData as $siswa) {
            if ($siswa->prakerin && $siswa->prakerin->count() > $maxPrakerin) {
                $maxPrakerin = $siswa->prakerin->count();
            }
        }

        // Tambahkan header untuk setiap prakerin
        for ($i = 1; $i <= $maxPrakerin; $i++) {
            $headers = array_merge($headers, [
                "Perusahaan $i",
                "Pembimbing Sekolah $i",
                "Pembimbing Perusahaan $i",
                "Tanggal Mulai $i",
                "Tanggal Selesai $i",
                "Status Prakerin $i"
            ]);
        }

        // Data
        foreach ($siswaData as $index => $siswa) {
            $rowData = [
                $index + 1,
                $siswa->nis ?? '-',
                $siswa->nama_siswa ?? '-',
                $siswa->kelas ? $siswa->kelas->nama_kelas : '-'
            ];

            // Tambahkan informasi untuk setiap prakerin
            $prakerinCount = 0;
            if ($siswa->prakerin) {
                foreach ($siswa->prakerin as $prakerin) {
                    // Filter berdasarkan periode jika diperlukan
                    $shouldInclude = true;
                    if ($period !== 'all' && $period !== 'custom') {
                        $prakerinDate = Carbon::parse($prakerin->tanggal_mulai);
                        switch ($period) {
                            case 'today':
                                $shouldInclude = $prakerinDate->isToday();
                                break;
                            case '3days':
                                $shouldInclude = $prakerinDate >= Carbon::now()->subDays(3);
                                break;
                            case '7days':
                                $shouldInclude = $prakerinDate >= Carbon::now()->subDays(7);
                                break;
                            case '1month':
                                $shouldInclude = $prakerinDate >= Carbon::now()->subMonth();
                                break;
                            case '1year':
                                $shouldInclude = $prakerinDate >= Carbon::now()->subYear();
                                break;
                        }
                    } else if ($period === 'custom') {
                        // Untuk periode custom, filter berdasarkan tanggal
                        if (!empty($this->startDate) && !empty($this->endDate)) {
                            $prakerinDate = Carbon::parse($prakerin->tanggal_mulai);
                            $startDate = Carbon::parse($this->startDate)->startOfDay();
                            $endDate = Carbon::parse($this->endDate)->endOfDay();
                            $shouldInclude = $prakerinDate >= $startDate && $prakerinDate <= $endDate;
                        } else {
                            $shouldInclude = true; // Jika tanggal tidak diisi, tampilkan semua
                        }
                    }

                    if ($shouldInclude) {
                        // Perbaiki cara menampilkan pembimbing sekolah
                        $pembimbingSekolahNama = '-';
                        if (!is_null($prakerin->pembimbingSekolah) && isset($prakerin->pembimbingSekolah->nama_pembimbing_sekolah)) {
                            $pembimbingSekolahNama = $prakerin->pembimbingSekolah->nama_pembimbing_sekolah;
                        }

                        // Perbaiki cara menampilkan pembimbing perusahaan
                        $pembimbingPerusahaanNama = '-';
                        if (!is_null($prakerin->pembimbingPerusahaan) && isset($prakerin->pembimbingPerusahaan->nama)) {
                            $pembimbingPerusahaanNama = $prakerin->pembimbingPerusahaan->nama;
                        }

                        // Perbaiki cara menampilkan perusahaan
                        $namaPerusahaan = '-';
                        if (!is_null($prakerin->perusahaan)) {
                            $namaPerusahaan = $prakerin->perusahaan->nama_perusahaan ?? '-';
                        }

                        $rowData = array_merge($rowData, [
                            $namaPerusahaan,
                            $pembimbingSekolahNama,
                            $pembimbingPerusahaanNama,
                            $prakerin->tanggal_mulai ? Carbon::parse($prakerin->tanggal_mulai)->format('d/m/Y') : '-',
                            $prakerin->tanggal_selesai ? Carbon::parse($prakerin->tanggal_selesai)->format('d/m/Y') : '-',
                            ucfirst($prakerin->status_prakerin) ?? '-'
                        ]);
                        
                        $prakerinCount++;
                    }
                }
            }

            // Tambahkan kolom kosong untuk prakerin yang tidak ada
            for ($i = $prakerinCount; $i < $maxPrakerin; $i++) {
                $rowData = array_merge($rowData, [
                    '-', '-', '-', '-', '-', '-'
                ]);
            }

            $data[] = $rowData;
        }

        // Gabungkan header dan data
        $exportData = array_merge([$headers], $data);

        // Buat file Excel dengan nama yang mencerminkan periode
        $periodLabel = match($period) {
            'today' => 'hari_ini',
            '3days' => '3_hari_terakhir',
            '7days' => '7_hari_terakhir',
            '1month' => '1_bulan_terakhir',
            '1year' => '1_tahun_terakhir',
            'custom' => 'periode_' . ($this->startDate ?? 'start') . '_sampai_' . ($this->endDate ?? 'end'),
            default => 'semua_data'
        };
        
        $filename = 'data_prakerin_siswa_' . $periodLabel . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        // Reset tanggal setelah export selesai (hanya untuk periode custom)
        if ($period === 'custom') {
            $this->reset(['startDate', 'endDate']);
        }
        
        return Excel::download(new \App\Exports\DataExport($exportData), $filename);
    }

    public function resetCustomDates()
    {
        $this->reset(['startDate', 'endDate']);
    }

    public function exportExcelPerusahaan()
    {
        // Ambil semua data perusahaan dengan relasi
        $perusahaanData = Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
            ->orderBy('nama_perusahaan')
            ->get();

        // Format data untuk Excel
        $data = [];
        
        // Header
        $headers = [
            'No',
            'Nama Perusahaan',
            'Alamat',
            'Email',
            'Kontak',
            'Jumlah Siswa Diterima',
            'Pembimbing Sekolah',
            'Pembimbing Perusahaan'
        ];

        // Data
        foreach ($perusahaanData as $index => $perusahaan) {
            // Hitung jumlah siswa yang diterima di perusahaan ini
            $jumlahSiswa = Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
                ->where('status_pengajuan', 'diterima_perusahaan')
                ->count();

            // Perbaiki cara menampilkan pembimbing sekolah
            $pembimbingSekolahNama = '-';
            if (!is_null($perusahaan->pembimbingSekolah) && isset($perusahaan->pembimbingSekolah->nama_pembimbing_sekolah)) {
                $pembimbingSekolahNama = $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah;
            }

            // Perbaiki cara menampilkan pembimbing perusahaan
            $pembimbingPerusahaanNama = '-';
            if (!is_null($perusahaan->pembimbingPerusahaan) && $perusahaan->pembimbingPerusahaan->isNotEmpty()) {
                $pembimbingPerusahaanNama = $perusahaan->pembimbingPerusahaan->pluck('nama')->implode(', ');
            }

            // Pastikan nilai numerik diformat dengan benar
            $jumlahSiswaFormatted = (int)$jumlahSiswa;

            $data[] = [
                $index + 1,
                $perusahaan->nama_perusahaan ?? '-',
                $perusahaan->alamat_perusahaan ?? '-',
                $perusahaan->email_perusahaan ?? '-',
                $perusahaan->kontak_perusahaan ?? '-',
                $jumlahSiswaFormatted,
                $pembimbingSekolahNama,
                $pembimbingPerusahaanNama
            ];
        }

        // Gabungkan header dan data
        $exportData = array_merge([$headers], $data);

        // Buat file Excel
        $filename = 'data_perusahaan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new \App\Exports\DataExport($exportData), $filename);
    }

    public function render()
    {
        $this->historyPengajuan = $this->getHistoryPengajuan();
        return view('livewire.staff-hubin.dashboard');
    }
}