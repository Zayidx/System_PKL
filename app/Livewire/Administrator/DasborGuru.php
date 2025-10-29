<?php

namespace App\Livewire\Administrator;

use App\Models\Guru;
use App\Models\User;
use App\Models\Role;
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
#[Title("Manajemen Guru")]
/**
 * Mengelola data guru dan akun terkait:
 * - Menyediakan pencarian & paginasi agar admin mudah menemukan guru.
 * - Memfasilitasi pembuatan serta pembaruan guru sekaligus akun user (role admin sekolah).
 * - Mengatur unggahan foto profil dan pembersihan file lama.
 * - Mengirimkan feedback ke UI melalui event SweetAlert setelah operasi selesai.
 */
class DasborGuru extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $guruId, $userId;
    public $nip_guru, $nama_guru, $kontak_guru, $email;
    public $password, $password_confirmation;
    public $foto, $existingFoto;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    protected function rules()
    {
        $passwordRule = $this->guruId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->guruId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nip_guru' => ['required', 'numeric', 'digits_between:4,18', Rule::unique('guru', 'nip_guru')->ignore($this->guruId, 'nip_guru')],
            'nama_guru' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'kontak_guru' => ['required', 'numeric', 'digits_between:10,15'],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    protected function messages()
    {
        return [
            'nip_guru.required' => 'NIP/ID Guru wajib diisi.',
            'nip_guru.unique' => 'NIP/ID Guru ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'password.required' => 'Password wajib diisi untuk guru baru.',
            'foto.required' => 'Foto profil wajib diunggah untuk guru baru.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar guru terbaru lengkap dengan relasi user.
     * Pencarian dilakukan pada nama, NIP, maupun email agar fleksibel.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $guruData = Guru::with('user')
            ->where('nama_guru', 'like', $searchTerm)
            ->orWhere('nip_guru', 'like', $searchTerm)
            ->orWhereHas('user', function ($query) use ($searchTerm) {
                $query->where('email', 'like', $searchTerm);
            })
            ->latest('nip_guru')
            ->paginate($this->perPage);

        return view('livewire.administrator.dasbor-guru', ['guruData' => $guruData]);
    }

    /**
     * Membuka modal formulir dalam kondisi kosong untuk menambah guru baru.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi ulang properti formulir berdasarkan guru yang dipilih.
     * - Memuat relasi user untuk mengambil email dan foto saat ini.
     * - Menyimpan URL foto lama supaya bisa ditampilkan di modal.
     */
    public function edit($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        $this->guruId = $guru->nip_guru;
        $this->userId = $guru->user_id;

        $this->nip_guru = $guru->nip_guru;
        $this->nama_guru = $guru->nama_guru;
        $this->kontak_guru = $guru->kontak_guru;
        
        if ($guru->user) {
            $this->email = $guru->user->email;
            $this->existingFoto = $guru->user->foto ? Storage::url($guru->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan perubahan data guru.
     * Prosedurnya digabung dalam `DB::transaction` agar update user dan guru
     * berjalan atomik:
     * - Mode edit (`$this->guruId` terisi) memperbarui akun user beserta foto
     *   bila ada unggahan baru, lalu menyinkronkan data guru.
     * - Mode tambah membuat akun user dengan role admin, mengunggah foto ke
     *   `storage/app/public/fotos/profil`, kemudian membuat entri guru yang
     *   terhubung.
     * Setelah sukses, event SweetAlert dikirim dan modal ditutup untuk memberi
     * umpan balik ke pengguna.
     */
    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->guruId) { // Update
                $guru = Guru::findOrFail($this->guruId);
                $user = $guru->user;

                $userData = ['username' => $validatedData['nama_guru'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) {
                    $userData['password'] = Hash::make($validatedData['password']);
                }
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);

                $guru->update(['nip_guru' => $validatedData['nip_guru'], 'nama_guru' => $validatedData['nama_guru'], 'kontak_guru' => $validatedData['kontak_guru']]);
            } else { // Create
                $adminRole = Role::where('name', 'admin')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');

                $newUser = User::create(['roles_id' => $adminRole->id, 'username' => $validatedData['nama_guru'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                Guru::create(['nip_guru' => $validatedData['nip_guru'], 'user_id' => $newUser->id, 'nama_guru' => $validatedData['nama_guru'], 'kontak_guru' => $validatedData['kontak_guru']]);
            }
        });

        $this->dispatch('swal:success', ['message' => $this->guruId ? 'Data guru berhasil diperbarui.' : 'Guru baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-guru')]
    /**
     * Menghapus guru beserta akun user yang terhubung dan file fotonya.
     * Event SweetAlert dipicu supaya pengguna melihat pesan keberhasilan.
     */
    public function destroy($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        if ($guru->user && $guru->user->foto) {
            Storage::disk('public')->delete($guru->user->foto);
        }
        if ($guru->user) $guru->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data guru berhasil dihapus.']);
    }

    /**
     * Menutup modal dan mereset error agar form siap dipakai ulang.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mengembalikan semua properti formulir ke kondisi awal, termasuk
     * mengosongkan URL foto lama dan pesan validasi.
     */
    private function resetForm()
    {
        $this->reset(['isModalOpen', 'guruId', 'userId', 'nip_guru', 'nama_guru', 'kontak_guru', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto']);
        $this->resetErrorBag();
    }
}
