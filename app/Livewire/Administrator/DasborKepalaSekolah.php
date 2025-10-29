<?php

namespace App\Livewire\Administrator;

use App\Models\KepalaSekolah;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Manajemen Kepala Sekolah")]
/**
 * Modul admin untuk mengelola kepala sekolah:
 * - Menggabungkan data profil, jabatan, dan akun login dalam satu antarmuka.
 * - Memungkinkan unggah foto profil dengan pengelolaan file lama.
 * - Menolak penghapusan bila masih terkait dengan data monitoring.
 */
class DasborKepalaSekolah extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $isModalOpen = false;
    public $kepsekId, $userId;
    public $nip_kepsek, $nama_kepala_sekolah, $jabatan, $email;
    public $password, $password_confirmation;
    public $foto, $existingFoto;
    public $search = '';
    public $perPage = 10;

    /**
     * Validasi agar NIP, email, dan jabatan kepala sekolah tetap unik & konsisten.
     */
    protected function rules()
    {
        $passwordRule = $this->kepsekId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->kepsekId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nip_kepsek' => ['required', 'string', 'max:60', Rule::unique('kepala_sekolah', 'nip_kepsek')->ignore($this->kepsekId, 'id_kepsek')],
            'nama_kepala_sekolah' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    /**
     * Menyiapkan daftar kepala sekolah beserta relasi user untuk ditampilkan.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $kepsekData = KepalaSekolah::with('user')
            ->where('nama_kepala_sekolah', 'like', $searchTerm)
            ->orWhere('nip_kepsek', 'like', $searchTerm)
            ->orWhereHas('user', fn($q) => $q->where('email', 'like', $searchTerm))
            ->latest('id_kepsek')
            ->paginate($this->perPage);

        return view('livewire.administrator.dasbor-kepala-sekolah', ['kepsekData' => $kepsekData]);
    }

    /**
     * Membuka modal tambah data dengan membersihkan form terlebih dahulu.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi formulir dengan data kepala sekolah yang dipilih.
     */
    public function edit($id)
    {
        $kepsek = KepalaSekolah::with('user')->findOrFail($id);
        $this->kepsekId = $kepsek->id_kepsek;
        $this->userId = $kepsek->user_id;
        $this->nip_kepsek = $kepsek->nip_kepsek;
        $this->nama_kepala_sekolah = $kepsek->nama_kepala_sekolah;
        $this->jabatan = $kepsek->jabatan;
        if ($kepsek->user) {
            $this->email = $kepsek->user->email;
            $this->existingFoto = $kepsek->user->foto ? Storage::url($kepsek->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    /**
     * Membuat atau memperbarui kepala sekolah menggunakan `DB::transaction`
     * agar perubahan pada tabel user dan kepala sekolah terjadi bersamaan.
     * Termasuk mengelola unggahan foto baru dan penghapusan foto lama.
     * Setelah berhasil, modal ditutup dan notifikasi SweetAlert ditampilkan.
     */
    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->kepsekId) { // Update
                $kepsek = KepalaSekolah::findOrFail($this->kepsekId);
                $user = $kepsek->user;
                $userData = ['username' => $validatedData['nama_kepala_sekolah'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) $userData['password'] = Hash::make($validatedData['password']);
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);
                $kepsek->update(['nip_kepsek' => $validatedData['nip_kepsek'], 'nama_kepala_sekolah' => $validatedData['nama_kepala_sekolah'], 'jabatan' => $validatedData['jabatan']]);
            } else { // Create
                $kepsekRole = Role::where('name', 'kepalasekolah')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');
                $newUser = User::create(['roles_id' => $kepsekRole->id, 'username' => $validatedData['nama_kepala_sekolah'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                KepalaSekolah::create(['user_id' => $newUser->id, 'nip_kepsek' => $validatedData['nip_kepsek'], 'nama_kepala_sekolah' => $validatedData['nama_kepala_sekolah'], 'jabatan' => $validatedData['jabatan']]);
            }
        });

        $this->dispatch('swal:success', ['message' => $this->kepsekId ? 'Data berhasil diperbarui.' : 'Data baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-kepala-sekolah')]
    /**
     * Menghapus kepala sekolah setelah memastikan tidak ada relasi monitoring
     * yang masih bergantung, sekaligus membersihkan akun dan foto.
     */
    public function destroy($id)
    {
        $kepsek = KepalaSekolah::with(['user', 'monitoring'])->findOrFail($id);
        if ($kepsek->monitoring()->exists()) {
            $this->dispatch('swal:error', ['message' => 'Gagal! Data ini terkait dengan data Monitoring.']);
            return;
        }
        if ($kepsek->user && $kepsek->user->foto) Storage::disk('public')->delete($kepsek->user->foto);
        if ($kepsek->user) $kepsek->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data kepala sekolah berhasil dihapus.']);
    }

    /**
     * Menutup modal dan mengembalikan form ke keadaan semula.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Reset seluruh properti form sekaligus menghapus pesan error validasi.
     */
    private function resetForm()
    {
        $this->reset();
        $this->resetErrorBag();
    }
}
