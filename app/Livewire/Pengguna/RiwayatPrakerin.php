<?php

namespace App\Livewire\Pengguna;

use App\Models\Prakerin;
use App\Models\Pengajuan;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('components.layouts.layout-user-dashboard')]
#[Title('Riwayat Prakerin')]
/**
 * Riwayat prakerin siswa:
 * - Menampilkan daftar prakerin dan pengajuan beserta pencarian & paginasi.
 * - Menyediakan aksi menyelesaikan/batalkan prakerin serta pengajuan ulang.
 * - Memfasilitasi perpanjangan prakerin langsung dari riwayat.
 */
class RiwayatPrakerin extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    // Properti untuk modal perpanjangan
    public $showModalPerpanjangan = false;
    public $selectedPrakerinId = null;
    public $selectedPerusahaanId = null;
    public $tanggalMulaiPerpanjangan;
    public $tanggalSelesaiPerpanjangan;
    public $keteranganPerpanjangan;
    public $perusahaanSelesai = [];

    /**
     * Reset halaman ketika kata kunci berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil data prakerin dan pengajuan siswa, sekaligus memperbarui daftar perusahaan selesai.
     */
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

        // Ambil daftar perusahaan yang sudah diselesaikan untuk perpanjangan
        $this->perusahaanSelesai = Prakerin::with('perusahaan')
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->get()
            ->unique('id_perusahaan')
            ->pluck('perusahaan')
            ->filter();

        return view('livewire.pengguna.riwayat-prakerin', [
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

    /**
     * Menandai prakerin sebagai selesai setelah memverifikasi kepemilikan dan status aktif.
     */
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

    /**
     * Membatalkan prakerin aktif milik siswa bila ditemukan.
     */
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

    /**
     * Mengarahkan pengguna ke halaman pengajuan ulang jika tidak ada prakerin aktif.
     */
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

        return redirect()->route('pengguna.pengajuan', $idPerusahaan);
    }

    /**
     * Buka modal perpanjangan prakerin
     */
    public function bukaModalPerpanjangan($idPrakerin)
    {
        $prakerin = Prakerin::where('id_prakerin', $idPrakerin)
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->first();

        if (!$prakerin) {
            $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan atau tidak dapat diperpanjang.']);
            return;
        }

        // Cek apakah siswa sudah memiliki prakerin aktif
        $prakerinAktif = Prakerin::where('nis_siswa', Auth::user()->siswa->nis)
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
     * Listener untuk event dari dashboard
     */
    #[On('bukaModalPerpanjangan')]
    public function handleBukaModalPerpanjangan($data)
    {
        $this->bukaModalPerpanjangan($data['idPrakerin']);
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
        $prakerinAktif = Prakerin::where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'aktif')
            ->first();
            
        if ($prakerinAktif) {
            $this->dispatch('swal:error', ['message' => 'Anda masih memiliki prakerin aktif. Selesaikan prakerin terlebih dahulu sebelum memperpanjang.']);
            return;
        }

        // Ambil data prakerin yang akan diperpanjang
        $prakerinLama = Prakerin::where('id_prakerin', $this->selectedPrakerinId)
            ->where('nis_siswa', Auth::user()->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->first();

        if (!$prakerinLama) {
            $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan atau tidak dapat diperpanjang.']);
            return;
        }

        try {
            // Buat prakerin baru dengan data dari prakerin lama
            Prakerin::create([
                'nis_siswa' => Auth::user()->siswa->nis,
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
            
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Terjadi kesalahan saat memperpanjang prakerin. Silakan coba lagi.']);
        }
    }

    /**
     * Kirim form penilaian ke perusahaan
     */
    public function kirimFormPenilaian($idPrakerin)
    {
        \Illuminate\Support\Facades\Log::info('Method kirimFormPenilaian dipanggil', [
            'id_prakerin' => $idPrakerin,
            'user_id' => Auth::id(),
            'nis' => Auth::user()->siswa->nis ?? 'N/A'
        ]);

        // Debug: Cek apakah method benar-benar dipanggil
        \Illuminate\Support\Facades\Log::info('DEBUG: Method kirimFormPenilaian mulai eksekusi');

        try {
            $prakerin = Prakerin::with(['siswa', 'perusahaan', 'pembimbingPerusahaan'])
                ->where('id_prakerin', $idPrakerin)
                ->where('nis_siswa', Auth::user()->siswa->nis)
                ->where('status_prakerin', 'selesai')
                ->first();

            \Illuminate\Support\Facades\Log::info('Query prakerin selesai', [
                'prakerin_found' => $prakerin ? true : false,
                'prakerin_id' => $prakerin->id_prakerin ?? null,
                'status' => $prakerin->status_prakerin ?? null
            ]);

            if (!$prakerin) {
                \Illuminate\Support\Facades\Log::warning('Prakerin tidak ditemukan', [
                    'id_prakerin' => $idPrakerin,
                    'nis' => Auth::user()->siswa->nis
                ]);
                $this->dispatch('swal:error', ['message' => 'Prakerin tidak ditemukan atau tidak dapat mengirim form penilaian.']);
                return;
            }

            \Illuminate\Support\Facades\Log::info('Prakerin ditemukan, lanjut ke cek penilaian existing');

            // Cek apakah sudah ada penilaian
            \Illuminate\Support\Facades\Log::info('Cek penilaian existing');
            $existingPenilaian = \App\Models\Penilaian::where('nis_siswa', $prakerin->nis_siswa)
                ->where('id_pemb_perusahaan', $prakerin->id_pembimbing_perusahaan)
                ->first();

            \Illuminate\Support\Facades\Log::info('Hasil cek penilaian existing', [
                'existing_found' => $existingPenilaian ? true : false,
                'nis_siswa' => $prakerin->nis_siswa,
                'id_pemb_perusahaan' => $prakerin->id_pembimbing_perusahaan
            ]);

            if ($existingPenilaian) {
                \Illuminate\Support\Facades\Log::warning('Penilaian sudah ada');
                $this->dispatch('swal:error', ['message' => 'Form penilaian sudah pernah dikirim untuk prakerin ini.']);
                return;
            }

            \Illuminate\Support\Facades\Log::info('Penilaian belum ada, lanjut ke cek kompetensi');

            // Ambil kompetensi berdasarkan jurusan siswa
            \Illuminate\Support\Facades\Log::info('Cek kompetensi untuk jurusan', [
                'id_jurusan' => $prakerin->siswa->id_jurusan
            ]);
            $kompetensi = \App\Models\Kompetensi::where('id_jurusan', $prakerin->siswa->id_jurusan)->get();

            \Illuminate\Support\Facades\Log::info('Hasil cek kompetensi', [
                'kompetensi_count' => $kompetensi->count(),
                'id_jurusan' => $prakerin->siswa->id_jurusan
            ]);

            if ($kompetensi->isEmpty()) {
                \Illuminate\Support\Facades\Log::warning('Kompetensi tidak ditemukan');
                $this->dispatch('swal:error', ['message' => 'Kompetensi tidak ditemukan untuk jurusan ini.']);
                return;
            }

            \Illuminate\Support\Facades\Log::info('Kompetensi ditemukan, lanjut ke generate token');

            // Generate token
            \Illuminate\Support\Facades\Log::info('Generate token');
            $token = \Illuminate\Support\Str::random(60);
            \Illuminate\Support\Facades\Log::info('Token generated', ['token' => $token]);
            
            // Simpan token ke cache
            \Illuminate\Support\Facades\Log::info('Simpan token ke cache');
            \Illuminate\Support\Facades\Cache::put("penilaian_token_{$token}", [
                'prakerin_id' => $prakerin->id_prakerin,
                'nis_siswa' => $prakerin->nis_siswa,
                'pembimbing_id' => $prakerin->id_pembimbing_perusahaan,
                'expires_at' => now()->addDays(7)
            ], now()->addDays(7));
            \Illuminate\Support\Facades\Log::info('Token berhasil disimpan ke cache');

            // Kirim email
            \Illuminate\Support\Facades\Log::info('Kirim email', [
                'email_to' => $prakerin->perusahaan->email_perusahaan,
                'perusahaan' => $prakerin->perusahaan->nama_perusahaan
            ]);
            
            try {
                \Illuminate\Support\Facades\Mail::to($prakerin->perusahaan->email_perusahaan)
                    ->send(new \App\Mail\PenilaianFormEmail(
                        $prakerin, 
                        $prakerin->siswa, 
                        $prakerin->perusahaan, 
                        $prakerin->pembimbingPerusahaan, 
                        $kompetensi, 
                        $token
                    ));
                \Illuminate\Support\Facades\Log::info('Email berhasil dikirim');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error kirim email', [
                    'error' => $e->getMessage(),
                    'email_to' => $prakerin->perusahaan->email_perusahaan
                ]);
                throw $e;
            }

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
