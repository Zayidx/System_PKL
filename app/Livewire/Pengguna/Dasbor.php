<?php

namespace App\Livewire\Pengguna;

use App\Models\Pengajuan;
use App\Models\Prakerin;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout("components.layouts.layout-user-dashboard")]
#[Title('Dasbor Siswa')]
/**
 * Dasbor siswa terpadu:
 * - Menampilkan statistik pengajuan, status magang aktif, dan pengajuan terbaru.
 * - Menyediakan modal perpanjangan prakerin dengan validasi lengkap.
 * - Menangani pembaruan otomatis prakerin yang sudah melewati tanggal selesai.
 */
class Dasbor extends Component
{
    // Properti dari Dashboard asli
    public $user;
    public $siswa;
    public $totalPengajuan = 0;
    public $diterimaCount = 0;
    public $ditolakCount = 0;
    public $pendingCount = 0;
    public $recentPengajuan = [];

    // Properti dari ProsesMagang
    public $prakerinData;
    public $pengajuanDiterimaData;

    // Properti untuk modal perpanjangan
    public $showModalPerpanjangan = false;
    public $selectedPrakerinId = null;
    public $selectedPerusahaanId = null;
    public $tanggalMulaiPerpanjangan;
    public $tanggalSelesaiPerpanjangan;
    public $keteranganPerpanjangan;
    public $perusahaanSelesai = [];

    /**
     * Lifecycle hook yang berjalan saat komponen pertama kali dimuat.
     * Mengambil dan memproses semua data yang diperlukan untuk dashboard gabungan.
     */
    public function mount()
    {
        // Inisialisasi koleksi untuk data magang
        $this->prakerinData = collect();
        $this->pengajuanDiterimaData = collect();

        // Mengambil data user yang login beserta relasi siswa
        $user = Auth::user()->load(['siswa.kelas', 'siswa.jurusan']);
        $this->user = $user;
        $this->siswa = $user->siswa;

        // Lanjutkan hanya jika data siswa ada
        if ($this->siswa) {
            // 1. Cek dan update prakerin yang sudah lewat waktu
            $this->updateExpiredPrakerin();
            
            // 2. Mengambil semua pengajuan untuk statistik dan aktivitas terbaru
            $semuaPengajuan = $this->siswa->pengajuan()->with('perusahaan')->get();

            // Menghitung statistik pengajuan
            $this->totalPengajuan = $semuaPengajuan->count();
            $this->diterimaCount = $semuaPengajuan->where('status_pengajuan', 'diterima_perusahaan')->count();
            $this->ditolakCount = $semuaPengajuan->whereIn('status_pengajuan', ['ditolak_admin', 'ditolak_perusahaan'])->count();
            $this->pendingCount = $semuaPengajuan->whereIn('status_pengajuan', ['pending', 'diterima_admin'])->count();
            
            // Mengambil 5 pengajuan terbaru untuk ditampilkan
            $this->recentPengajuan = $semuaPengajuan->sortByDesc('id_pengajuan')->take(5);

            // 3. Logika dari ProsesMagang: Cek status magang aktif
            // Memuat relasi yang dibutuhkan untuk detail magang
            $this->prakerinData = Prakerin::with([
                'perusahaan.pembimbingSekolah',
                'pembimbingPerusahaan'
            ])
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->latest('tanggal_mulai')
            ->get();

            // 4. Jika tidak ada magang aktif, cek pengajuan yang sudah diterima perusahaan
            if ($this->prakerinData->isEmpty()) {
                $this->pengajuanDiterimaData = Pengajuan::with('perusahaan.pembimbingSekolah')
                    ->where('nis_siswa', $this->siswa->nis)
                    ->where('status_pengajuan', 'diterima_perusahaan')
                    ->latest('updated_at')
                    ->get();
            }
        }
    }

   
    /**
     * Mengubah status prakerin yang sudah melewati tanggal selesai menjadi "selesai".
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
     * Merender view komponen dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Ambil daftar perusahaan yang sudah diselesaikan untuk perpanjangan
        if ($this->siswa) {
            $this->perusahaanSelesai = Prakerin::with('perusahaan')
                ->where('nis_siswa', $this->siswa->nis)
                ->where('status_prakerin', 'selesai')
                ->get()
                ->unique('id_perusahaan')
                ->pluck('perusahaan')
                ->filter();
        }

        return view('livewire.pengguna.dasbor');
    }

    /**
     * Buka modal perpanjangan prakerin
     */
    public function bukaModalPerpanjangan($idPrakerin)
    {
        $prakerin = Prakerin::where('id_prakerin', $idPrakerin)
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->first();

        if (!$prakerin) {
            $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan atau tidak dapat diperpanjang.']);
            return;
        }

        // Cek apakah siswa sudah memiliki prakerin aktif
        $prakerinAktif = Prakerin::where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->first();
            
        if ($prakerinAktif) {
            $this->dispatch('swal:error', ['message' => 'Anda masih memiliki prakerin aktif. Selesaikan prakerin terlebih dahulu sebelum memperpanjang.']);
            return;
        }

        $this->selectedPrakerinId = $idPrakerin;
        $this->selectedPerusahaanId = $prakerin->id_perusahaan;
        $this->tanggalMulaiPerpanjangan = null;
        $this->tanggalSelesaiPerpanjangan = null;
        $this->keteranganPerpanjangan = null;
        $this->showModalPerpanjangan = true;
    }

    /**
     * Tutup modal perpanjangan
     */
    public function tutupModalPerpanjangan()
    {
        $this->showModalPerpanjangan = false;
        $this->selectedPrakerinId = null;
        $this->selectedPerusahaanId = null;
        $this->tanggalMulaiPerpanjangan = null;
        $this->tanggalSelesaiPerpanjangan = null;
        $this->keteranganPerpanjangan = null;
    }

    /**
     * Proses perpanjangan prakerin
     */
    public function prosesPerpanjangan()
    {
        // Validasi input
        $this->validate([
            'tanggalMulaiPerpanjangan' => 'required|date|after_or_equal:today',
            'tanggalSelesaiPerpanjangan' => 'required|date|after:tanggalMulaiPerpanjangan',
            'keteranganPerpanjangan' => 'nullable|string|max:500',
        ], [
            'tanggalMulaiPerpanjangan.required' => 'Tanggal mulai perpanjangan wajib diisi.',
            'tanggalMulaiPerpanjangan.after_or_equal' => 'Tanggal mulai perpanjangan harus hari ini atau setelahnya.',
            'tanggalSelesaiPerpanjangan.required' => 'Tanggal selesai perpanjangan wajib diisi.',
            'tanggalSelesaiPerpanjangan.after' => 'Tanggal selesai perpanjangan harus setelah tanggal mulai.',
        ]);

        // Cek apakah siswa sudah memiliki prakerin aktif
        $prakerinAktif = Prakerin::where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->first();
            
        if ($prakerinAktif) {
            $this->dispatch('swal:error', ['message' => 'Anda masih memiliki prakerin aktif. Selesaikan prakerin terlebih dahulu sebelum memperpanjang.']);
            return;
        }

        // Ambil data prakerin yang akan diperpanjang
        $prakerinLama = Prakerin::where('id_prakerin', $this->selectedPrakerinId)
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->first();

        if (!$prakerinLama) {
            $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan atau tidak dapat diperpanjang.']);
            return;
        }

        try {
            // Buat prakerin baru dengan data dari prakerin lama
            Prakerin::create([
                'nis_siswa' => $this->siswa->nis,
                'nip_pembimbing_sekolah' => $prakerinLama->nip_pembimbing_sekolah,
                'id_pembimbing_perusahaan' => $prakerinLama->id_pembimbing_perusahaan,
                'id_perusahaan' => $prakerinLama->id_perusahaan,
                'nip_kepala_program' => $prakerinLama->nip_kepala_program,
                'tanggal_mulai' => $this->tanggalMulaiPerpanjangan,
                'tanggal_selesai' => $this->tanggalSelesaiPerpanjangan,
                'keterangan' => $this->keteranganPerpanjangan ?? 'Perpanjangan prakerin dari periode sebelumnya',
                'status_prakerin' => 'aktif',
            ]);

            $this->tutupModalPerpanjangan();
            $this->dispatch('swal:success', ['message' => 'Prakerin berhasil diperpanjang!']);
            
            // Refresh data
            $this->mount();
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Terjadi kesalahan saat memperpanjang prakerin. Silakan coba lagi.']);
        }
    }

    /**
     * Kirim form penilaian ke perusahaan
     */
    public function kirimFormPenilaian($idPrakerin)
    {
        try {
            $prakerin = Prakerin::with(['siswa', 'perusahaan', 'pembimbingPerusahaan'])
                ->where('id_prakerin', $idPrakerin)
                ->where('nis_siswa', $this->siswa->nis)
                ->where('status_prakerin', 'selesai')
                ->first();

            if (!$prakerin) {
                $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan atau tidak dapat mengirim form penilaian.']);
                return;
            }

            // Cek apakah sudah ada penilaian
            $existingPenilaian = \App\Models\Penilaian::where('nis_siswa', $prakerin->nis_siswa)
                ->where('id_pemb_perusahaan', $prakerin->id_pembimbing_perusahaan)
                ->first();

            if ($existingPenilaian) {
                $this->dispatch('swal:error', ['message' => 'Form penilaian sudah pernah dikirim untuk prakerin ini.']);
                return;
            }

            // Ambil kompetensi berdasarkan jurusan siswa
            $kompetensi = \App\Models\Kompetensi::where('id_jurusan', $prakerin->siswa->id_jurusan)->get();

            if ($kompetensi->isEmpty()) {
                $this->dispatch('swal:error', ['message' => 'Kompetensi tidak ditemukan untuk jurusan ini.']);
                return;
            }

            // Generate token
            $token = \Illuminate\Support\Str::random(60);
            
            // Simpan token ke cache
            \Illuminate\Support\Facades\Cache::put("penilaian_token_{$token}", [
                'prakerin_id' => $prakerin->id_prakerin,
                'nis_siswa' => $prakerin->nis_siswa,
                'pembimbing_id' => $prakerin->id_pembimbing_perusahaan,
                'expires_at' => now()->addDays(7)
            ], now()->addDays(7));

            // Kirim email
            \Illuminate\Support\Facades\Mail::to($prakerin->perusahaan->email_perusahaan)
                ->send(new \App\Mail\PenilaianFormEmail(
                    $prakerin, 
                    $prakerin->siswa, 
                    $prakerin->perusahaan, 
                    $prakerin->pembimbingPerusahaan, 
                    $kompetensi, 
                    $token
                ));

            $this->dispatch('swal:success', [
                'message' => "Form penilaian berhasil dikirim ke {$prakerin->perusahaan->nama_perusahaan}!"
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error kirim form penilaian', [
                'error' => $e->getMessage(),
                'prakerin_id' => $idPrakerin
            ]);
            
            $this->dispatch('swal:error', [
                'message' => 'Terjadi kesalahan saat mengirim form penilaian. Silakan coba lagi.'
            ]);
        }
    }
}
