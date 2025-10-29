<?php

namespace App\Livewire\Pengguna;

use Livewire\Component;
use App\Models\MitraPerusahaanPending;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout("components.layouts.layout-user-dashboard")]
#[Title("Manajemen Perusahaan")]
/**
 * Formulir bagi siswa untuk mengusulkan mitra perusahaan baru:
 * - Memvalidasi data dasar perusahaan sebelum diajukan.
 * - Menolak pengajuan duplikat yang masih menunggu konfirmasi.
 * - Mengirim data ke tabel pending dan memberi umpan balik ke siswa.
 */
class AjukanPerusahaanBaru extends Component
{
    public $nama_mitra;
    public $alamat_mitra;
    public $email_mitra;
    public $kontak_mitra;

    protected $rules = [
        'nama_mitra' => 'required|string|min:3',
        'alamat_mitra' => 'required|string|min:5',
        'email_mitra' => 'required|email',
        'kontak_mitra' => 'required|string|min:8',
    ];

    /**
     * Menyimpan pengajuan baru setelah validasi dan pengecekan duplikasi.
     */
    public function submit()
    {
        $this->validate();
        $nis = Auth::user()->siswa->nis;
        $sudahAda = MitraPerusahaanPending::where('nama_perusahaan', $this->nama_mitra)
            ->where('status', 'pending')
            ->exists();
        if ($sudahAda) {
            session()->flash('error', 'Pengajuan perusahaan ini sudah pernah diajukan dan menunggu konfirmasi.');
            return;
        }
        MitraPerusahaanPending::create([
            'nama_perusahaan' => $this->nama_mitra,
            'alamat_perusahaan' => $this->alamat_mitra,
            'email_perusahaan' => $this->email_mitra,
            'kontak_perusahaan' => $this->kontak_mitra,
            'status' => 'pending',
            'nis_pengaju' => $nis,
        ]);
        $this->reset(['nama_mitra', 'alamat_mitra', 'email_mitra', 'kontak_mitra']);
        session()->flash('success', 'Pengajuan perusahaan berhasil dikirim, menunggu konfirmasi staff hubin.');
    }

    /**
     * Menampilkan tampilan Livewire formulir pengajuan mitra.
     */
    public function render()
    {
        return view('livewire.pengguna.ajukan-perusahaan-baru');
    }
}
