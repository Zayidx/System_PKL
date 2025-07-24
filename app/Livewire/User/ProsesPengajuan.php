<?php

namespace App\Livewire\User;

use App\Models\Perusahaan;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Pengajuan Pemagangan')]
class ProsesPengajuan extends Component
{
    public Perusahaan $perusahaan;
    public ?Pengajuan $pengajuan;

    // Gunakan atribut Rule untuk validasi yang lebih bersih
    #[Rule('required|date|after_or_equal:today', message: 'Tanggal mulai tidak boleh tanggal yang sudah lewat.')]
    public $tanggal_mulai;

    #[Rule('required|date|after_or_equal:tanggal_mulai', message: 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.')]
    public $tanggal_selesai;

    #[Rule('required|url', message: 'Link CV harus berupa URL yang valid.')]
    public $link_cv;

    public int $durasi_hari = 0;
    public int $durasi_bulan = 0;

    public function mount($id_perusahaan)
    {
        $this->perusahaan = Perusahaan::findOrFail($id_perusahaan);
        $this->pengajuan = Pengajuan::where('nis_siswa', Auth::user()->siswa->nis)
            ->where('id_perusahaan', $id_perusahaan)
            ->first();
    }

    // Livewire akan otomatis memanggil method ini setiap kali properti 'tanggal_mulai' diperbarui
    public function updatedTanggalMulai()
    {
        $this->hitungDurasi();
    }

    // Livewire akan otomatis memanggil method ini setiap kali properti 'tanggal_selesai' diperbarui
    public function updatedTanggalSelesai()
    {
        $this->hitungDurasi();
    }

    private function hitungDurasi()
    {
        // Validasi sementara untuk memastikan input adalah tanggal yang valid sebelum di-parse
        if (!strtotime($this->tanggal_mulai) || !strtotime($this->tanggal_selesai)) {
            $this->durasi_hari = 0;
            $this->durasi_bulan = 0;
            return;
        }

        $mulai = Carbon::parse($this->tanggal_mulai);
        $selesai = Carbon::parse($this->tanggal_selesai);

        // Pastikan tanggal selesai tidak sebelum tanggal mulai
        if ($selesai->isBefore($mulai)) {
            $this->durasi_hari = 0;
            $this->durasi_bulan = 0;
            return;
        }

        // Hitung durasi. Tambah 1 untuk membuatnya inklusif (contoh: 1-3 Juli dihitung 3 hari)
        $this->durasi_hari = $mulai->diffInDays($selesai) + 1;
        $this->durasi_bulan = $mulai->diffInMonths($selesai) + ($mulai->day <= $selesai->day ? 1 : 0);
        
        // Jika dalam bulan yang sama, hitung sebagai 1 bulan
        if ($mulai->isSameMonth($selesai)) {
            $this->durasi_bulan = 1;
        } else {
             // Kalkulasi bulan yang lebih logis
            $this->durasi_bulan = $mulai->diffInMonths($selesai);
            if ($selesai->day >= $mulai->day) {
                $this->durasi_bulan += 1;
            }
        }

    }

    public function konfirmasiPengajuan()
    {
        // Validasi akan menggunakan atribut Rule di atas
        $this->validate();

        $this->dispatch('swal:ajukan-proses', [
            'nama' => $this->perusahaan->nama_perusahaan,
        ]);
    }

    // Gunakan atribut On untuk listener yang lebih modern
    #[On('prosesAjukanMagang')]
    public function ajukanMagang()
    {
        // Jalankan validasi sekali lagi sebelum menyimpan
        $this->validate();

        $year = date('Y');
        if (
            date('Y', strtotime($this->tanggal_mulai)) != $year ||
            date('Y', strtotime($this->tanggal_selesai)) != $year
        ) {
            $this->addError('tanggal_mulai', 'Tanggal PKL harus di tahun berjalan.');
            $this->addError('tanggal_selesai', 'Tanggal PKL harus di tahun berjalan.');
            return;
        }

        try {
            Pengajuan::create([
                'nis_siswa' => Auth::user()->siswa->nis,
                'id_perusahaan' => $this->perusahaan->id_perusahaan,
                'status_pengajuan' => 'pending',
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'link_cv' => $this->link_cv,
            ]);

            $this->dispatch('swal:success', ['message' => 'Pengajuan berhasil dikirim!']);
            
            // Refresh data pengajuan setelah berhasil
            $this->pengajuan = Pengajuan::where('nis_siswa', Auth::user()->siswa->nis)
                ->where('id_perusahaan', $this->perusahaan->id_perusahaan)
                ->first();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menyimpan pengajuan. Silakan coba lagi.']);
            // Opsional: log error untuk debugging
            // Log::error('Gagal menyimpan pengajuan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.proses-pengajuan');
    }
}
