<?php

namespace App\Livewire\Administrator;

use App\Models\Role;
use App\Models\User;
use App\Models\WaliKelas;
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
#[Title("Manajemen Wali Kelas")]
/**
 * Modul admin untuk mengelola wali kelas:
 * - Mengatur akun login serta informasi dasar wali kelas.
 * - Menjaga integritas dengan mencegah penghapusan ketika masih mengampu kelas.
 * - Mengelola foto profil melalui storage publik.
 */
class DasborWaliKelas extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $waliKelasId, $userId;
    public $nip_wali_kelas, $nama_wali_kelas, $email;
    public $password, $password_confirmation;
    public $foto, $existingFoto;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Validasi dinamis agar NIP, email, dan foto diproses sesuai konteks tambah/edit.
     */
    protected function rules()
    {
        $passwordRule = $this->waliKelasId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->waliKelasId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            // NIP sekarang menjadi input manual, bukan lagi pilihan
            'nip_wali_kelas' => ['required', 'numeric', 'digits_between:4,18', Rule::unique('wali_kelas', 'nip_wali_kelas')->ignore($this->waliKelasId, 'nip_wali_kelas')],
            'nama_wali_kelas' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    /**
     * Pesan validasi khusus agar input error mudah dipahami.
     */
    protected function messages()
    {
        return [
            'nip_wali_kelas.required' => 'NIP/ID Wali Kelas wajib diisi.',
            'nip_wali_kelas.unique' => 'NIP/ID ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nama_wali_kelas.required' => 'Nama wali kelas wajib diisi.',
            'password.required' => 'Password wajib diisi untuk akun baru.',
            'foto.required' => 'Foto profil wajib diunggah untuk akun baru.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar wali kelas beserta relasi user & kelas untuk ditampilkan di tabel Livewire.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $waliKelasData = WaliKelas::with(['user', 'kelas'])
            ->where('nama_wali_kelas', 'like', $searchTerm)
            ->orWhere('nip_wali_kelas', 'like', $searchTerm)
            ->orWhereHas('user', fn($q) => $q->where('email', 'like', $searchTerm))
            ->latest('nip_wali_kelas')
            ->paginate($this->perPage);

        return view('livewire.administrator.dasbor-wali-kelas', ['waliKelasData' => $waliKelasData]);
    }

    /**
     * Membuka modal tambah dengan mereset seluruh field formulir.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi form dengan data wali kelas yang dipilih agar dapat diperbarui.
     */
    public function edit($id)
    {
        $waliKelas = WaliKelas::with('user')->findOrFail($id);
        $this->waliKelasId = $waliKelas->nip_wali_kelas;
        $this->userId = $waliKelas->user_id;
        $this->nip_wali_kelas = $waliKelas->nip_wali_kelas;
        $this->nama_wali_kelas = $waliKelas->nama_wali_kelas;
        
        if ($waliKelas->user) {
            $this->email = $waliKelas->user->email;
            $this->existingFoto = $waliKelas->user->foto ? Storage::url($waliKelas->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan data wali kelas menggunakan transaksi agar perubahan user dan wali kelas sinkron.
     * Termasuk mengganti foto profil bila diunggah ulang, lalu memberikan feedback via SweetAlert.
     */
    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->waliKelasId) { // Update
                $waliKelas = WaliKelas::findOrFail($this->waliKelasId);
                $user = $waliKelas->user;

                $userData = ['username' => $validatedData['nama_wali_kelas'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) $userData['password'] = Hash::make($validatedData['password']);
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);
                $waliKelas->update(['nip_wali_kelas' => $validatedData['nip_wali_kelas'], 'nama_wali_kelas' => $validatedData['nama_wali_kelas']]);

            } else { // Create
                $waliKelasRole = Role::where('name', 'walikelas')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');

                $newUser = User::create(['roles_id' => $waliKelasRole->id, 'username' => $validatedData['nama_wali_kelas'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                WaliKelas::create(['nip_wali_kelas' => $validatedData['nip_wali_kelas'], 'user_id' => $newUser->id, 'nama_wali_kelas' => $validatedData['nama_wali_kelas']]);
            }
        });

        $this->dispatch('swal:success', ['message' => $this->waliKelasId ? 'Data wali kelas berhasil diperbarui.' : 'Wali kelas baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-wali-kelas')]
    /**
     * Menghapus wali kelas jika tidak lagi mengampu kelas, sekaligus menghapus akun dan foto terkait.
     */
    public function destroy($id)
    {
        $waliKelas = WaliKelas::with(['user', 'kelas'])->findOrFail($id);

        if ($waliKelas->kelas) {
            $this->dispatch('swal:error', ['message' => 'Gagal! Wali kelas ini masih mengampu kelas ' . $waliKelas->kelas->nama_kelas . '.']);
            return;
        }

        if ($waliKelas->user && $waliKelas->user->foto) Storage::disk('public')->delete($waliKelas->user->foto);
        if ($waliKelas->user) $waliKelas->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data wali kelas berhasil dihapus.']);
    }

    /**
     * Menutup modal dan mereset form/error.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mengembalikan seluruh properti formulir ke kondisi awal.
     */
    private function resetForm()
    {
        $this->reset(['isModalOpen', 'waliKelasId', 'userId', 'nip_wali_kelas', 'nama_wali_kelas', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto']);
        $this->resetErrorBag();
    }
}
