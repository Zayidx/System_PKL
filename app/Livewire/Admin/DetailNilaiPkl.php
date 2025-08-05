<?php

namespace App\Livewire\Admin;

use App\Models\Penilaian;
use App\Models\Kompetensi;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;

#[Layout("components.layouts.layout-admin-dashboard")]
class DetailNilaiPkl extends Component
{
    public $penilaianId;
    public $penilaian;
    public $kompetensiWithNilai;
    public $nilaiRataRata;
    public $nilaiTertinggi;
    public $nilaiTerendah;

    public function mount($id)
    {
        $this->penilaianId = $id;
        $this->loadPenilaianData();
    }

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

    public function render()
    {
        return view('livewire.admin.detail-nilai-pkl');
    }
} 