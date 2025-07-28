<?php

namespace App\Livewire\StaffHubin;

use Livewire\Component;
use App\Models\MitraPerusahaanPending;
use App\Models\Perusahaan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Monitoring Pengajuan')]
class MitraPerusahaan extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $catatan_staff = [];

    public function approve($id)
    {
        $mitra = MitraPerusahaanPending::findOrFail($id);
        // Cek duplikat di perusahaan
        $sudahAda = Perusahaan::where('nama_perusahaan', $mitra->nama_perusahaan)->exists();
        if ($sudahAda) {
            $mitra->status = 'rejected';
            $mitra->catatan_staff = 'Nama perusahaan sudah terdaftar.';
            $mitra->save();
            $this->dispatch('swal:error', ['message' => 'Nama perusahaan sudah terdaftar di database utama.']);
            return;
        }
        // Tambahkan ke tabel perusahaan
        Perusahaan::create([
            'nama_perusahaan' => $mitra->nama_perusahaan,
            'alamat_perusahaan' => $mitra->alamat_perusahaan,
            'email_perusahaan' => $mitra->email_perusahaan,
            'kontak_perusahaan' => $mitra->kontak_perusahaan,
        ]);
        $mitra->status = 'approved';
        $mitra->catatan_staff = $this->catatan_staff[$id] ?? null;
        $mitra->save();
        $this->dispatch('swal:success', ['message' => 'Perusahaan berhasil di-ACC dan masuk ke daftar perusahaan terdaftar.']);
    }

    public function reject($id)
    {
        $mitra = MitraPerusahaanPending::findOrFail($id);
        $mitra->status = 'rejected';
        $mitra->catatan_staff = $this->catatan_staff[$id] ?? null;
        $mitra->save();
        $this->dispatch('swal:success', ['message' => 'Pengajuan perusahaan ditolak.']);
    }

    public function render()
    {
        $mitraList = MitraPerusahaanPending::where('status', 'pending')->orderByDesc('created_at')->paginate(10);
        return view('livewire.staff-hubin.mitra-perusahaan', [
            'mitraList' => $mitraList,
        ]);
    }
}
