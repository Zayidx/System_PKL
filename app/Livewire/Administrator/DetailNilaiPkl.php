<?php

namespace App\Livewire\Administrator;

use App\Models\Penilaian;
use App\Models\Kompetensi;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

#[Layout("components.layouts.layout-admin-dashboard")]
/**
 * Menyajikan detail nilai PKL per penilaian:
 * - Menampilkan informasi siswa, pembimbing, serta nilai per kompetensi.
 * - Menghitung statistik pendukung seperti rata-rata, nilai tertinggi, dan terendah.
 * - Menyediakan helper untuk menerjemahkan skor menjadi keterangan teks.
 */
class DetailNilaiPkl extends Component
{
    public $penilaianId;
    public $penilaian;
    public $kompetensiWithNilai;
    public $nilaiRataRata;
    public $nilaiTertinggi;
    public $nilaiTerendah;

    /**
     * Menyimpan ID penilaian dan langsung memuat data detailnya.
     */
    public function mount($id)
    {
        $this->penilaianId = $id;
        $this->loadPenilaianData();
    }

    /**
     * Mengambil penilaian beserta relasi, menghitung statistik nilai,
     * dan mencatat hasil ke log untuk keperluan audit.
     */
    public function loadPenilaianData()
    {
        try {
            $this->penilaian = Penilaian::with([
                'siswa',
                'pembimbingPerusahaan',
                'kompetensi'
            ])->findOrFail($this->penilaianId);

            $this->kompetensiWithNilai = $this->penilaian->kompetensi()->withPivot('nilai')->get();
            $this->nilaiRataRata = $this->kompetensiWithNilai->avg('pivot.nilai');
            $this->nilaiTertinggi = $this->kompetensiWithNilai->max('pivot.nilai');
            $this->nilaiTerendah = $this->kompetensiWithNilai->min('pivot.nilai');

            Log::info('Detail nilai PKL berhasil dimuat', [
                'penilaian_id' => $this->penilaianId,
                'siswa_nis' => $this->penilaian->siswa->nis ?? null,
                'jumlah_kompetensi' => $this->kompetensiWithNilai->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error memuat detail nilai PKL', [
                'error' => $e->getMessage(),
                'penilaian_id' => $this->penilaianId
            ]);

            session()->flash('error', 'Terjadi kesalahan saat memuat detail nilai PKL.');
        }
    }

    /**
     * Mengubah nilai numerik menjadi keterangan teks & kelas CSS untuk badge.
     */
    public function getKeteranganNilai($nilai)
    {
        if ($nilai >= 85) {
            return ['text' => 'Sangat Baik', 'class' => 'text-success', 'badge' => 'bg-success'];
        } elseif ($nilai >= 75) {
            return ['text' => 'Baik', 'class' => 'text-warning', 'badge' => 'bg-warning'];
        } else {
            return ['text' => 'Perlu Perbaikan', 'class' => 'text-danger', 'badge' => 'bg-danger'];
        }
    }

    /**
     * Mengonversi nilai rata-rata menjadi keterangan dan warna teks.
     */
    public function getKeteranganRataRata($nilai)
    {
        if ($nilai >= 85) {
            return ['text' => 'Sangat Baik', 'class' => 'text-success'];
        } elseif ($nilai >= 75) {
            return ['text' => 'Baik', 'class' => 'text-warning'];
        } else {
            return ['text' => 'Perlu Perbaikan', 'class' => 'text-danger'];
        }
    }

    /**
     * Mengirim data ke tampilan detail Livewire.
     */
    public function render()
    {
        return view('livewire.administrator.detail-nilai-pkl');
    }
} 
