<?php

namespace App\Livewire\Administrator;

use App\Models\Kelas;
use App\Models\WaliKelas;
use App\Models\Jurusan;
use App\Models\Angkatan;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Manajemen Kelas")]
/**
 * Modul admin untuk mengelola struktur kelas:
 * - Menampilkan daftar kelas lengkap dengan wali, jurusan, dan angkatan.
 * - Memungkinkan admin menambah/mengedit kelas beserta relasi utamanya.
 * - Menjaga integritas data dengan mencegah penghapusan kelas yang masih memiliki siswa.
 */
class DasborKelas extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $kelasId;
    public $nama_kelas, $tingkat_kelas, $nip_wali_kelas, $id_jurusan, $id_angkatan;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Aturan validasi.
     * Nama kelas harus unik berdasarkan kombinasi tingkat, jurusan, dan angkatan.
     */
    protected function rules()
    {
        return [
            'nama_kelas' => ['required', 'string', 'max:10'],
            'tingkat_kelas' => ['required', 'string', 'max:5'],
            'nip_wali_kelas' => ['required', 'exists:wali_kelas,nip_wali_kelas'],
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan'],
            'id_angkatan' => ['required', 'exists:angkatan,id_angkatan'],
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected function messages()
    {
        return [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'tingkat_kelas.required' => 'Tingkat kelas wajib diisi.',
            'nip_wali_kelas.required' => 'Wali kelas wajib dipilih.',
            'id_jurusan.required' => 'Jurusan wajib dipilih.',
            'id_angkatan.required' => 'Angkatan wajib dipilih.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Render komponen dengan data kelas dan opsi untuk dropdown.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $kelasData = Kelas::with(['waliKelas', 'jurusan', 'angkatan', 'siswa'])
            ->where('nama_kelas', 'like', $searchTerm)
            ->orWhere('tingkat_kelas', 'like', $searchTerm)
            ->orWhereHas('jurusan', fn($q) => $q->where('nama_jurusan_lengkap', 'like', $searchTerm))
            ->orWhereHas('waliKelas', fn($q) => $q->where('nama_wali_kelas', 'like', $searchTerm))
            ->latest('id_kelas')
            ->paginate($this->perPage);

        // Ambil data untuk dropdown di modal
        $waliKelasOptions = WaliKelas::orderBy('nama_wali_kelas')->get();
        $jurusanOptions = Jurusan::orderBy('nama_jurusan_lengkap')->get();
        $angkatanOptions = Angkatan::orderBy('tahun', 'desc')->get();

        return view('livewire.administrator.dasbor-kelas', [
            'kelasData' => $kelasData,
            'waliKelasOptions' => $waliKelasOptions,
            'jurusanOptions' => $jurusanOptions,
            'angkatanOptions' => $angkatanOptions,
        ]);
    }

    /**
     * Membuka modal kosong untuk proses pembuatan kelas baru.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Memuat data kelas yang akan diperbarui agar form menampilkan nilai terkini.
     */
    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        $this->kelasId = $id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tingkat_kelas = $kelas->tingkat_kelas;
        $this->nip_wali_kelas = $kelas->nip_wali_kelas;
        $this->id_jurusan = $kelas->id_jurusan;
        $this->id_angkatan = $kelas->id_angkatan;
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan data baru atau memperbarui data yang sudah ada.
     */
    public function store()
    {
        $validatedData = $this->validate();

        Kelas::updateOrCreate(['id_kelas' => $this->kelasId], $validatedData);

        $this->dispatch('swal:success', [
            'message' => $this->kelasId ? 'Data kelas berhasil diperbarui.' : 'Kelas baru berhasil ditambahkan.'
        ]);

        $this->closeModal();
    }

    /**
     * Menghapus data kelas.
     */
    #[On('destroy-kelas')]
    public function destroy($id)
    {
        $kelas = Kelas::withCount('siswa')->findOrFail($id);

        // PENTING: Cek apakah ada siswa di kelas ini.
        if ($kelas->siswa_count > 0) {
            $this->dispatch('swal:error', [
                'message' => 'Gagal! Kelas ini tidak dapat dihapus karena masih memiliki ' . $kelas->siswa_count . ' siswa.'
            ]);
            return;
        }

        $kelas->delete();
        $this->dispatch('swal:success', ['message' => 'Data kelas berhasil dihapus.']);
    }

    /**
     * Menutup modal dan membersihkan input/error agar form siap digunakan kembali.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mengatur ulang semua properti formulir ke nilai default.
     */
    private function resetForm()
    {
        $this->reset(['isModalOpen', 'kelasId', 'nama_kelas', 'tingkat_kelas', 'nip_wali_kelas', 'id_jurusan', 'id_angkatan']);
        $this->resetErrorBag();
    }
}
