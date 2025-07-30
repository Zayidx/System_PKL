<?php

namespace App\Livewire\User;

use App\Models\Prakerin;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

// Menentukan layout utama dan judul halaman untuk komponen ini.
#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Informasi Magang')]
class ProsesMagang extends Component
{
    /**
     * Menampung data prakerin aktif jika ada.
     * @var \Illuminate\Support\Collection
     */
    public $prakerinData;

    /**
     * Menampung data pengajuan yang diterima jika tidak ada prakerin aktif.
     * @var \Illuminate\Support\Collection
     */
    public $pengajuanData;

    /**
     * Menampung data siswa yang sedang login.
     * @var \App\Models\Siswa|null
     */
    public $siswa;

    /**
     * Method yang dipanggil saat komponen pertama kali di-mount.
     * Berfungsi untuk memuat data awal.
     * Logika di sini sudah efisien dan tidak memerlukan perubahan.
     */
    public function mount()
    {
        $this->prakerinData = collect();
        $this->pengajuanData = collect();
        $this->loadData();
    }

    /**
     * Memuat data proses magang untuk siswa yang terautentikasi.
     * Method ini mengatur logika pengambilan data, pertama mencari status prakerin aktif,
     * jika tidak ada, baru mencari status pengajuan yang diterima.
     * Penggunaan eager loading ('with') sudah tepat untuk optimasi query.
     */
    public function loadData()
    {
        $user = Auth::user();

        // Pastikan user terautentikasi dan memiliki relasi dengan data siswa.
        if ($user && $user->siswa) {
            // Eager load relasi 'kelas' dan 'jurusan' untuk mencegah N+1 query problem di view.
            $this->siswa = $user->siswa()->with(['kelas', 'jurusan'])->first();

            // 1. Update prakerin yang sudah lewat waktu menjadi selesai
            $this->updateExpiredPrakerin();

            // 2. Cek data prakerin yang aktif untuk siswa ini.
            // Memuat semua relasi yang dibutuhkan untuk menghindari query tambahan.
            $this->prakerinData = Prakerin::with([
                'perusahaan.pembimbingSekolah',
                'pembimbingPerusahaan'
            ])
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->latest('tanggal_mulai')
            ->get();

            // 3. Cek data pengajuan yang diterima.
            // Ini merepresentasikan kondisi di mana siswa sudah diterima tapi belum mulai magang.
            $this->pengajuanData = Pengajuan::with('perusahaan.pembimbingSekolah')
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_pengajuan', 'diterima_perusahaan')
            ->latest('updated_at')
            ->get();
        }
    }

    /**
     * Update prakerin yang sudah lewat waktu menjadi selesai
     */
    private function updateExpiredPrakerin()
    {
        $expiredPrakerin = Prakerin::where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->where('tanggal_selesai', '<', now())
            ->get();

        foreach ($expiredPrakerin as $prakerin) {
            $prakerin->update(['status_prakerin' => 'selesai']);
        }
    }

    /**
     * Merender view komponen.
     * View akan secara otomatis di-hydrate dengan public properties:
     * $prakerinData, $pengajuanData, dan $siswa.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.user.proses-magang');
    }
}
