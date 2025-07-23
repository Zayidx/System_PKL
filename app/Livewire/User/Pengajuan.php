<?php

namespace App\Livewire\User;

use App\Models\Pengajuan as PengajuanModel;
use App\Models\Perusahaan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini

#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Pengajuan Pemagangan')]
class Pengajuan extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $pengajuanSiswa = [];

    /**
     * Menambahkan listener untuk event konfirmasi dari SweetAlert.
     */
    protected $listeners = ['confirmAjukanMagang'];

    public function mount()
    {
        $this->loadPengajuanSiswa();
    }

    /**
     * Metode ini sekarang menerima parameter $id secara langsung,
     * yang namanya cocok dengan key data yang dikirim dari JavaScript.
     */
    public function confirmAjukanMagang($id)
    {
        // $idPerusahaan sekarang bisa langsung digunakan dari parameter $id.
        $idPerusahaan = $id;
        $nis = Auth::user()->siswa->nis;

        Log::info("Memulai proses pengajuan untuk NIS: {$nis} ke Perusahaan ID: {$idPerusahaan}");

        // Cek 1: Apakah sudah pernah mengajukan ke perusahaan ini?
        $sudahMengajukan = PengajuanModel::where('nis_siswa', $nis)->where('id_perusahaan', $idPerusahaan)->exists();
        if ($sudahMengajukan) {
            Log::warning("Pengajuan GAGAL untuk NIS: {$nis}. Alasan: Sudah pernah mengajukan ke perusahaan ID: {$idPerusahaan}.");
            $this->dispatch('swal:error', ['message' => 'Anda sudah pernah mengajukan ke perusahaan ini.']);
            return;
        }

        // Cek 2: Apakah sudah ada pengajuan yang diterima di tempat lain?
        // Bagian ini dinonaktifkan sesuai permintaan agar bisa mengajukan ke banyak tempat.
        /*
        $sudahDiterima = collect($this->pengajuanSiswa)->where('status_pengajuan', 'diterima_perusahaan')->isNotEmpty();
        if ($sudahDiterima) {
            Log::warning("Pengajuan GAGAL untuk NIS: {$nis}. Alasan: Sudah diterima di perusahaan lain.");
            $this->dispatch('swal:error', ['message' => 'Anda sudah diterima di perusahaan lain dan tidak dapat mengajukan lagi.']);
            return;
        }
        */

        Log::info("Semua validasi berhasil untuk NIS: {$nis}. Membuat data pengajuan baru...");

        PengajuanModel::create([
            'nis_siswa' => $nis,
            'id_perusahaan' => $idPerusahaan,
            'status_pengajuan' => 'pending',
        ]);

        $this->loadPengajuanSiswa();
        
        Log::info("Pengajuan BERHASIL untuk NIS: {$nis} ke Perusahaan ID: {$idPerusahaan}.");
        $this->dispatch('swal:success', ['message' => 'Pengajuan berhasil dikirim!']);
    }
    
    private function loadPengajuanSiswa()
    {
        $this->pengajuanSiswa = PengajuanModel::where('nis_siswa', Auth::user()->siswa->nis)
            ->get()
            ->keyBy('id_perusahaan');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $perusahaanData = Perusahaan::where('nama_perusahaan', 'like', '%' . $this->search . '%')
            ->orWhere('alamat_perusahaan', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.user.pengajuan', [
            'perusahaanData' => $perusahaanData,
        ]);
    }
}
