<?php

namespace App\Livewire\User;

use App\Models\Prakerin;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Riwayat Prakerin')]
class RiwayatPrakerin extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Update prakerin yang sudah lewat waktu menjadi selesai
        $this->updateExpiredPrakerin();
        
        $searchTerm = '%' . $this->search . '%';
        
        $prakerinData = Prakerin::with(['siswa', 'perusahaan', 'pembimbingSekolah', 'pembimbingPerusahaan'])
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->where(function($query) use ($searchTerm) {
                $query->whereHas('perusahaan', function($q) use ($searchTerm) {
                    $q->where('nama_perusahaan', 'like', $searchTerm);
                })
                ->orWhere('status_prakerin', 'like', $searchTerm);
            })
            ->latest('tanggal_mulai')
            ->paginate($this->perPage);

        $pengajuanData = Pengajuan::with(['perusahaan'])
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->where(function($query) use ($searchTerm) {
                $query->whereHas('perusahaan', function($q) use ($searchTerm) {
                    $q->where('nama_perusahaan', 'like', $searchTerm);
                })
                ->orWhere('status_pengajuan', 'like', $searchTerm);
            })
            ->latest('created_at')
            ->paginate($this->perPage);

        return view('livewire.user.riwayat-prakerin', [
            'prakerinData' => $prakerinData,
            'pengajuanData' => $pengajuanData,
        ]);
    }

    /**
     * Update prakerin yang sudah lewat waktu menjadi selesai
     */
    private function updateExpiredPrakerin()
    {
        $expiredPrakerin = Prakerin::where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->where('tanggal_selesai', '<', now())
            ->get();

        foreach ($expiredPrakerin as $prakerin) {
            $prakerin->update(['status_prakerin' => 'selesai']);
        }
    }

    public function selesaiPrakerin($idPrakerin)
    {
        $prakerin = Prakerin::where('id_prakerin', $idPrakerin)
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->first();

        if (!$prakerin) {
            $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan.']);
            return;
        }

        if ($prakerin->status_prakerin !== 'aktif') {
            $this->dispatch('swal:error', ['message' => 'Prakerin ini tidak aktif.']);
            return;
        }

        $prakerin->update(['status_prakerin' => 'selesai']);
        
        $this->dispatch('swal:success', ['message' => 'Prakerin berhasil diselesaikan. Sekarang Anda dapat mengajukan prakerin baru.']);
    }

    public function batalkanPrakerin($idPrakerin)
    {
        $prakerin = Prakerin::where('id_prakerin', $idPrakerin)
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->first();

        if (!$prakerin) {
            $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan.']);
            return;
        }

        if ($prakerin->status_prakerin !== 'aktif') {
            $this->dispatch('swal:error', ['message' => 'Prakerin ini tidak aktif.']);
            return;
        }

        $prakerin->update(['status_prakerin' => 'dibatalkan']);
        
        $this->dispatch('swal:success', ['message' => 'Prakerin berhasil dibatalkan. Sekarang Anda dapat mengajukan prakerin baru.']);
    }

    public function ajukanKembali($idPerusahaan)
    {
        // Cek apakah siswa sudah memiliki prakerin aktif
        $prakerinAktif = Prakerin::where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->first();
            
        if ($prakerinAktif) {
            $this->dispatch('swal:error', ['message' => 'Anda masih memiliki prakerin aktif. Selesaikan prakerin terlebih dahulu sebelum mengajukan yang baru.']);
            return;
        }

        return redirect()->route('user.pengajuan', $idPerusahaan);
    }
}
