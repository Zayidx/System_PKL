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
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $showModal = false;
    public $selectedPerusahaanId = null;
    public $link_cv;
    public $isPerusahaanTerdaftar = true;
    public $nama_perusahaan_manual;
    public $alamat_perusahaan_manual;
    public $showModalMitra = false;
    public $nama_mitra;
    public $alamat_mitra;
    public $email_mitra;
    public $kontak_mitra;

    protected $rules = [
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'link_cv' => 'required|url',
    ];

    protected $mitraRules = [
        'nama_mitra' => 'required|string|min:3',
        'alamat_mitra' => 'required|string|min:5',
        'email_mitra' => 'nullable|email',
        'kontak_mitra' => 'nullable|string|min:8',
    ];

    /**
     * Menambahkan listener untuk event konfirmasi dari SweetAlert.
     */
    protected $listeners = ['confirmAjukanMagangFinal' => 'ajukanMagangKontrak'];

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

        // Tampilkan modal input tanggal kontrak dan link CV
        $this->selectedPerusahaanId = $idPerusahaan;
        $this->tanggal_mulai = null;
        $this->tanggal_selesai = null;
        $this->link_cv = null;
        $this->showModal = true;
    }

    public function openModalPengajuan($id)
    {
        $this->selectedPerusahaanId = $id;
        $this->tanggal_mulai = null;
        $this->tanggal_selesai = null;
        $this->link_cv = null;
        $this->showModal = true;
    }

    public function openModalPengajuanManual()
    {
        $this->showModalMitra = true;
        $this->nama_mitra = null;
        $this->alamat_mitra = null;
        $this->email_mitra = null;
        $this->kontak_mitra = null;
    }

    public function konfirmasiPengajuanSetelahForm()
    {
        $rules = $this->rules;
        if (!$this->isPerusahaanTerdaftar) {
            $rules['nama_perusahaan_manual'] = 'required|string|min:3';
            $rules['alamat_perusahaan_manual'] = 'required|string|min:5';
        }
        $this->validate($rules);
        // Tampilkan SweetAlert konfirmasi
        $namaPerusahaan = $this->isPerusahaanTerdaftar
            ? (\App\Models\Perusahaan::find($this->selectedPerusahaanId)?->nama_perusahaan ?? 'Perusahaan')
            : $this->nama_perusahaan_manual;
        $this->dispatch('swal:ajukan-final', [
            'id' => $this->selectedPerusahaanId,
            'nama' => $namaPerusahaan,
        ]);
    }

    public function ajukanMagangKontrak()
    {
        if (
            !$this->tanggal_mulai ||
            !$this->tanggal_selesai ||
            !$this->link_cv
        ) {
            $this->addError('tanggal_mulai', 'Tanggal mulai wajib diisi.');
            $this->addError('tanggal_selesai', 'Tanggal selesai wajib diisi.');
            $this->addError('link_cv', 'Link CV wajib diisi.');
            return;
        }
        $nis = Auth::user()->siswa->nis;
        try {
            PengajuanModel::create([
                'nis_siswa' => $nis,
                'id_perusahaan' => $this->selectedPerusahaanId,
                'status_pengajuan' => 'pending',
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'link_cv' => $this->link_cv,
                'is_perusahaan_terdaftar' => $this->isPerusahaanTerdaftar,
                'nama_perusahaan_manual' => $this->isPerusahaanTerdaftar ? null : $this->nama_perusahaan_manual,
                'alamat_perusahaan_manual' => $this->isPerusahaanTerdaftar ? null : $this->alamat_perusahaan_manual,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menyimpan pengajuan. Silakan coba lagi.']);
            return;
        }
        $this->loadPengajuanSiswa();
        $this->showModal = false;
        $this->dispatch('swal:success', ['message' => 'Pengajuan berhasil dikirim!']);
    }

    public function ajukanMitraBaru()
    {
        $this->validate($this->mitraRules);
        $nis = Auth::user()->siswa->nis;
        // Cek duplikat pengajuan mitra
        $sudahAda = \App\Models\MitraPerusahaanPending::where('nama_perusahaan', $this->nama_mitra)
            ->where('status', 'pending')
            ->exists();
        if ($sudahAda) {
            $this->dispatch('swal:error', ['message' => 'Pengajuan perusahaan ini sudah pernah diajukan dan menunggu konfirmasi.']);
            return;
        }
        \App\Models\MitraPerusahaanPending::create([
            'nama_perusahaan' => $this->nama_mitra,
            'alamat_perusahaan' => $this->alamat_mitra,
            'email_perusahaan' => $this->email_mitra,
            'kontak_perusahaan' => $this->kontak_mitra,
            'status' => 'pending',
            'nis_pengaju' => $nis,
        ]);
        $this->showModalMitra = false;
        $this->dispatch('swal:success', ['message' => 'Pengajuan perusahaan berhasil dikirim, menunggu konfirmasi staff hubin.']);
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
