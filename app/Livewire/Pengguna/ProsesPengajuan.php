<?php

namespace App\Livewire\Pengguna;

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
/**
 * Formulir pengajuan magang untuk satu perusahaan:
 * - Memastikan siswa tidak memiliki prakerin aktif sebelum mengajukan.
 * - Menghitung durasi magang otomatis berdasarkan tanggal yang dipilih.
 * - Mengirimkan data pengajuan setelah konfirmasi SweetAlert.
 */
class ProsesPengajuan extends Component
{
    public Perusahaan $perusahaan;
    public ?Pengajuan $pengajuan;

    // Gunakan atribut Rule untuk validasi yang lebih bersih
    #[Rule('required|date', message: 'Tanggal mulai harus berupa tanggal yang valid.')]
    public $tanggal_mulai;

    #[Rule('required|date|after_or_equal:tanggal_mulai', message: 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.')]
    public $tanggal_selesai;

    #[Rule('required|url', message: 'Link CV harus berupa URL yang valid.')]
    public $link_cv;

    public int $durasi_hari = 0;
    public int $durasi_bulan = 0;

    /**
     * Memuat data perusahaan dan pengajuan terakhir siswa untuk perusahaan tersebut.
     * Juga memblokir akses jika siswa masih memiliki prakerin aktif.
     */
    public function mount($id_perusahaan)
    {
        $this->perusahaan = Perusahaan::findOrFail($id_perusahaan);
        
        // Cek apakah siswa sudah memiliki prakerin aktif
        $prakerinAktif = \App\Models\Prakerin::where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->first();
            
        if ($prakerinAktif) {
            $this->dispatch('swal:error', ['message' => 'Anda masih memiliki prakerin aktif. Selesaikan prakerin terlebih dahulu sebelum mengajukan yang baru.']);
            return redirect()->route('pengguna.dasbor');
        }
        
        // Ambil pengajuan terbaru untuk perusahaan ini (jika ada)
        $this->pengajuan = Pengajuan::where('nis_siswa', Auth::user()->siswa->nis)
            ->where('id_perusahaan', $id_perusahaan)
            ->latest('created_at')
            ->first();
    }

    // Livewire akan otomatis memanggil method ini setiap kali properti 'tanggal_mulai' diperbarui
    /**
     * Menghitung ulang durasi ketika tanggal mulai berubah.
     */
    public function updatedTanggalMulai()
    {
        $this->hitungDurasi();
    }

    // Livewire akan otomatis memanggil method ini setiap kali properti 'tanggal_selesai' diperbarui
    /**
     * Menghitung ulang durasi ketika tanggal selesai berubah.
     */
    public function updatedTanggalSelesai()
    {
        $this->hitungDurasi();
    }

    /**
     * Menghitung durasi hari/bulan secara inklusif dengan berbagai penyesuaian.
     */
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

    /**
     * Memvalidasi input dan memunculkan SweetAlert konfirmasi sebelum menyimpan.
     */
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
    /**
     * Listener yang menyimpan pengajuan setelah pengguna mengonfirmasi di SweetAlert.
     */
    public function ajukanMagang()
    {
        // Jalankan validasi sekali lagi sebelum menyimpan
        $this->validate();

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

    /**
     * Menyajikan tampilan proses pengajuan untuk perusahaan yang dipilih.
     */
    public function render()
    {
        return view('livewire.pengguna.proses-pengajuan');
    }
}
