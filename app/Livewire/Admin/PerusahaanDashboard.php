<?php

// [PERBAIKAN] Namespace disesuaikan dengan struktur folder yang benar
namespace App\Livewire\Admin;

use App\Models\Perusahaan;
use App\Models\PembimbingSekolah;
use App\Models\PembimbingPerusahaan;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Manajemen Perusahaan")]
class PerusahaanDashboard extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $perusahaanId, $nama_perusahaan, $alamat_perusahaan, $email_perusahaan, $kontak_perusahaan, $logo_perusahaan, $existingLogo;
    public $nip_pembimbing_sekolah;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Aturan validasi yang dinamis.
     * Logo wajib diisi saat membuat data baru, dan opsional saat mengedit.
     */
    protected function rules()
    {
        $logoRule = $this->perusahaanId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nama_perusahaan' => ['required', 'string', 'max:100', Rule::unique('perusahaan', 'nama_perusahaan')->ignore($this->perusahaanId, 'id_perusahaan')],
            'alamat_perusahaan' => ['required', 'string', 'max:255'],
            'email_perusahaan' => ['required', 'string', 'email', 'max:100', Rule::unique('perusahaan', 'email_perusahaan')->ignore($this->perusahaanId, 'id_perusahaan')],
            
            // [PERBAIKAN KRITIS] Aturan validasi untuk 'kontak_perusahaan' diubah total.
            // Sebelumnya: divalidasi sebagai email, yang mana salah.
            // Sekarang: divalidasi sebagai nomor (numeric), panjang antara 10-15 digit, dan unik di kolom 'kontak_perusahaan'.
            'kontak_perusahaan' => [
                'required', 
                'numeric', 
                'digits_between:10,15', 
                Rule::unique('perusahaan', 'kontak_perusahaan')->ignore($this->perusahaanId, 'id_perusahaan')
            ],

            'logo_perusahaan' => $logoRule,
            'nip_pembimbing_sekolah' => 'nullable|exists:pembimbing_sekolah,nip_pembimbing_sekolah',
        ];
    }

    /**
     * Pesan validasi kustom dalam bahasa Indonesia.
     */
    protected function messages()
    {
        return [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.unique' => 'Nama perusahaan ini sudah terdaftar.',
            'alamat_perusahaan.required' => 'Alamat perusahaan wajib diisi.',
            'email_perusahaan.required' => 'Email perusahaan wajib diisi.',
            'email_perusahaan.email' => 'Format email tidak valid.',
            'email_perusahaan.unique' => 'Email ini sudah terdaftar.',

            // [PENAMBAHAN] Pesan validasi baru untuk 'kontak_perusahaan'
            'kontak_perusahaan.required' => 'Kontak perusahaan wajib diisi.',
            'kontak_perusahaan.numeric' => 'Kontak perusahaan harus berupa angka.',
            'kontak_perusahaan.digits_between' => 'Kontak perusahaan harus antara 10 sampai 15 digit.',
            'kontak_perusahaan.unique' => 'Kontak perusahaan ini sudah terdaftar.',

            'logo_perusahaan.required' => 'Logo perusahaan wajib diunggah.',
            'logo_perusahaan.image' => 'File harus berupa gambar.',
            'logo_perusahaan.max' => 'Ukuran gambar maksimal 2MB.',
            'nip_pembimbing_sekolah.exists' => 'Pembimbing sekolah yang dipilih tidak valid.',
        ];
    }

    /**
     * Mereset halaman paginasi setiap kali ada perubahan pada properti $search.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Merender komponen dan mengambil data perusahaan dengan paginasi dan pencarian.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $perusahaanData = Perusahaan::where('nama_perusahaan', 'like', $searchTerm)
            ->orWhere('email_perusahaan', 'like', $searchTerm)
            // [PENAMBAHAN] Menambahkan pencarian berdasarkan kontak perusahaan agar lebih fungsional.
            ->orWhere('kontak_perusahaan', 'like', $searchTerm)
            ->latest('id_perusahaan')
            ->paginate($this->perPage);

        return view('livewire.admin.perusahaan-dashboard', [
            'perusahaanData' => $perusahaanData
        ]);
    }

    /**
     * Menyiapkan properti untuk membuka modal tambah data.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Menyiapkan properti untuk membuka modal edit data.
     */
    public function edit($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);
        $this->perusahaanId = $id;
        $this->nama_perusahaan = $perusahaan->nama_perusahaan;
        $this->alamat_perusahaan = $perusahaan->alamat_perusahaan;
        $this->email_perusahaan = $perusahaan->email_perusahaan;
        $this->kontak_perusahaan = $perusahaan->kontak_perusahaan;
        $this->existingLogo = $perusahaan->logo_perusahaan ? Storage::url($perusahaan->logo_perusahaan) : null;
        $this->nip_pembimbing_sekolah = $perusahaan->nip_pembimbing_sekolah;
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan data baru atau memperbarui data yang sudah ada.
     */
    public function store()
    {
        $validatedData = $this->validate();

        $data = [
            'nama_perusahaan' => $validatedData['nama_perusahaan'],
            'alamat_perusahaan' => $validatedData['alamat_perusahaan'],
            'email_perusahaan' => $validatedData['email_perusahaan'],
            'kontak_perusahaan' => $validatedData['kontak_perusahaan'],
            'nip_pembimbing_sekolah' => $validatedData['nip_pembimbing_sekolah'],
        ];

        if ($this->logo_perusahaan) {
            if ($this->perusahaanId) {
                $oldPerusahaan = Perusahaan::find($this->perusahaanId);
                if ($oldPerusahaan && $oldPerusahaan->logo_perusahaan) {
                    Storage::disk('public')->delete($oldPerusahaan->logo_perusahaan);
                }
            }
            $data['logo_perusahaan'] = $this->logo_perusahaan->store('logos_perusahaan', 'public');
        }

        Perusahaan::updateOrCreate(['id_perusahaan' => $this->perusahaanId], $data);
        
        $this->dispatch('swal:success', [
            'message' => $this->perusahaanId ? 'Data perusahaan berhasil diperbarui.' : 'Perusahaan baru berhasil ditambahkan.'
        ]);

        $this->closeModal();
    }

    /**
     * Menghapus data perusahaan berdasarkan ID.
     */
    #[On('destroy-perusahaan')]
    public function destroy($id)
    {
        $perusahaan = Perusahaan::findOrFail($id);

        if ($perusahaan->logo_perusahaan) {
            Storage::disk('public')->delete($perusahaan->logo_perusahaan);
        }

        $perusahaan->delete();

        $this->dispatch('swal:success', [
            'message' => 'Data perusahaan berhasil dihapus.'
        ]);
    }

    /**
     * Menutup modal dan mereset form.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mereset semua properti form dan error validasi.
     */
    private function resetForm()
    {
        // [PERBAIKAN] Menambahkan 'kontak_perusahaan' ke dalam array reset
        // agar field tidak menyimpan nilai lama saat modal ditutup dan dibuka lagi.
        $this->reset(['perusahaanId', 'nama_perusahaan', 'alamat_perusahaan', 'kontak_perusahaan', 'email_perusahaan', 'logo_perusahaan', 'existingLogo', 'nip_pembimbing_sekolah']);
        $this->resetErrorBag();
    }
}
