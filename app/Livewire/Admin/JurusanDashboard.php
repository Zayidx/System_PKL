<?php

namespace App\Livewire\Admin;

use App\Models\Jurusan;
use App\Models\KepalaProgram;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Manajemen Jurusan")]
class JurusanDashboard extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $jurusanId;
    public $nama_jurusan_lengkap, $nama_jurusan_singkat, $kepala_program;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Aturan validasi.
     */
    protected function rules()
    {
        return [
            'nama_jurusan_lengkap' => ['required', 'string', 'max:50', Rule::unique('jurusan', 'nama_jurusan_lengkap')->ignore($this->jurusanId, 'id_jurusan')],
            'nama_jurusan_singkat' => ['required', 'string', 'max:10', Rule::unique('jurusan', 'nama_jurusan_singkat')->ignore($this->jurusanId, 'id_jurusan')],
            'kepala_program' => ['nullable', 'exists:kepala_program,nip_kepala_program'],
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected function messages()
    {
        return [
            'nama_jurusan_lengkap.required' => 'Nama lengkap jurusan wajib diisi.',
            'nama_jurusan_lengkap.unique' => 'Nama jurusan ini sudah ada.',
            'nama_jurusan_singkat.required' => 'Singkatan jurusan wajib diisi.',
            'nama_jurusan_singkat.unique' => 'Singkatan ini sudah digunakan.',
            'kepala_program.exists' => 'Kepala program yang dipilih tidak valid.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Render komponen dengan data jurusan dan opsi untuk dropdown.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $jurusanData = Jurusan::with(['kepalaProgram'])->withCount(['kelas', 'siswa'])
            ->where('nama_jurusan_lengkap', 'like', $searchTerm)
            ->orWhere('nama_jurusan_singkat', 'like', $searchTerm)
            ->orWhereHas('kepalaProgram', fn($q) => $q->where('nama_kepala_program', 'like', $searchTerm))
            ->latest('id_jurusan')
            ->paginate($this->perPage);

        // Ambil data untuk dropdown di modal
        $kepalaProgramOptions = KepalaProgram::orderBy('nama_kepala_program')->get();

        return view('livewire.admin.jurusan-dashboard', [
            'jurusanData' => $jurusanData,
            'kepalaProgramOptions' => $kepalaProgramOptions,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $this->jurusanId = $id;
        $this->nama_jurusan_lengkap = $jurusan->nama_jurusan_lengkap;
        $this->nama_jurusan_singkat = $jurusan->nama_jurusan_singkat;
        $this->kepala_program = $jurusan->kepala_program;
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan data baru atau memperbarui data yang sudah ada.
     */
    public function store()
    {
        $validatedData = $this->validate();

        Jurusan::updateOrCreate(['id_jurusan' => $this->jurusanId], $validatedData);

        $this->dispatch('swal:success', [
            'message' => $this->jurusanId ? 'Data jurusan berhasil diperbarui.' : 'Jurusan baru berhasil ditambahkan.'
        ]);

        $this->closeModal();
    }

    /**
     * Menghapus data jurusan.
     */
    #[On('destroy-jurusan')]
    public function destroy($id)
    {
        $jurusan = Jurusan::withCount(['kelas', 'siswa'])->findOrFail($id);

        // PENTING: Cek apakah jurusan ini masih memiliki relasi.
        if ($jurusan->kelas_count > 0 || $jurusan->siswa_count > 0) {
            $this->dispatch('swal:error', [
                'message' => 'Gagal! Jurusan ini tidak dapat dihapus karena masih terkait dengan data kelas atau siswa.'
            ]);
            return;
        }

        $jurusan->delete();
        $this->dispatch('swal:success', ['message' => 'Data jurusan berhasil dihapus.']);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['isModalOpen', 'jurusanId', 'nama_jurusan_lengkap', 'nama_jurusan_singkat', 'kepala_program']);
        $this->resetErrorBag();
    }
}
