<?php

namespace App\Livewire\Pengguna;

use App\Models\Prakerin;
use App\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Proses Magang')]
/**
 * Menampilkan daftar prakerin siswa yang sedang berjalan dan menyediakan fitur perpanjangan.
 * - Menyaring prakerin berdasarkan nama perusahaan.
 * - Menginisialisasi tanggal perpanjangan default.
 * - Membuat prakerin baru berdasarkan perpanjangan dengan menjaga relasi pembimbing.
 */
class ProsesMagang extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedPrakerinId;
    public $showModalPerpanjangan = false;
    public $selectedPerusahaanId;
    public $tanggalMulaiBaru;
    public $tanggalSelesaiBaru;

    /**
     * Mengatur tanggal mulai/selesai default ketika komponen dimuat.
     */
    public function mount()
    {
        $this->tanggalMulaiBaru = now()->format('Y-m-d');
        $this->tanggalSelesaiBaru = now()->addMonths(3)->format('Y-m-d');
    }

    /**
     * Mengembalikan paginasi ke halaman pertama saat pencarian berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Menyimpan prakerin yang akan diperpanjang dan membuka modal input.
     */
    public function showPerpanjanganModal($prakerinId)
    {
        $this->selectedPrakerinId = $prakerinId;
        $this->showModalPerpanjangan = true;
    }

    /**
     * Memvalidasi permintaan perpanjangan dan membuat record prakerin baru
     * dengan relasi pembimbing serta perusahaan yang dipilih.
     */
    public function perpanjangPrakerin()
    {
        $this->validate([
            'selectedPerusahaanId' => 'required|exists:perusahaan,id_perusahaan',
            'tanggalMulaiBaru' => 'required|date|after_or_equal:today',
            'tanggalSelesaiBaru' => 'required|date|after:tanggalMulaiBaru',
        ]);

        $prakerinLama = Prakerin::findOrFail($this->selectedPrakerinId);
        
        // Ambil data pembimbing dari prakerin lama
        $pembimbingSekolah = $prakerinLama->pembimbingSekolah;
        $kepalaProgram = $prakerinLama->kepalaProgram;
        
        // Cari pembimbing perusahaan dari perusahaan yang dipilih
        $perusahaanBaru = \App\Models\Perusahaan::findOrFail($this->selectedPerusahaanId);
        $pembimbingPerusahaan = $perusahaanBaru->pembimbingPerusahaan;

        if (!$pembimbingPerusahaan) {
            session()->flash('error', 'Perusahaan yang dipilih tidak memiliki pembimbing perusahaan.');
            return;
        }

        // Buat prakerin baru
        Prakerin::create([
            'nis_siswa' => auth()->user()->siswa->nis,
            'id_perusahaan' => $this->selectedPerusahaanId,
            'id_pembimbing_perusahaan' => $pembimbingPerusahaan->id_pembimbing,
            'nip_pembimbing_sekolah' => $pembimbingSekolah->nip_pembimbing_sekolah,
            'nip_kepala_program' => $kepalaProgram->nip_kepala_program,
            'tanggal_mulai' => $this->tanggalMulaiBaru,
            'tanggal_selesai' => $this->tanggalSelesaiBaru,
            'status_prakerin' => 'aktif',
        ]);

        session()->flash('success', 'Prakerin berhasil diperpanjang dengan perusahaan baru.');
        $this->showModalPerpanjangan = false;
        $this->reset(['selectedPrakerinId', 'selectedPerusahaanId']);
    }

    /**
     * Mengambil daftar prakerin aktif dan perusahaan yang sudah selesai untuk opsi perpanjangan.
     */
    public function render()
    {
        $siswa = auth()->user()->siswa;
        
        $prakerinList = Prakerin::with(['perusahaan', 'pembimbingPerusahaan', 'pembimbingSekolah'])
            ->where('nis_siswa', $siswa->nis)
            ->when($this->search, function($query) {
                $query->whereHas('perusahaan', function($q) {
                    $q->where('nama_perusahaan', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate($this->perPage);

        // Ambil perusahaan dari prakerin yang sudah selesai untuk opsi perpanjangan
        $perusahaanSelesai = Prakerin::with('perusahaan')
            ->where('nis_siswa', $siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->where('tanggal_selesai', '<', now())
            ->get()
            ->pluck('perusahaan')
            ->unique('id_perusahaan')
            ->filter();

        return view('livewire.pengguna.proses-magang', [
            'prakerinList' => $prakerinList,
            'perusahaanSelesai' => $perusahaanSelesai,
        ]);
    }
}
