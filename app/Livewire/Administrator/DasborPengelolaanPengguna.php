<?php

namespace App\Livewire\Administrator;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout("components.layouts.layout-admin-dashboard")]
#[Title("Manajemen Pengguna")]
/**
 * Manajemen akun pengguna lintas peran:
 * - Menyediakan tabel dengan pencarian dan paginasi.
 * - Menggabungkan pembuatan, pembaruan, dan penghapusan akun beserta foto profil.
 * - Menjaga keamanan dengan mencegah manipulasi terhadap akun yang sedang login atau superadmin terakhir.
 */
class DasborPengelolaanPengguna extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $isModalOpen = false;
    public $userId, $username, $email, $password, $password_confirmation, $foto, $existingFoto;
    
    public $roles_id = null;

    public $roles;
    public $search = '';
    public $perPage = 10;

    /**
     * Validasi dinamis untuk memastikan akun unik serta foto wajib saat membuat user baru.
     */
    protected function rules()
    {
        $passwordRule = $this->userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed';
        
        // Foto wajib diisi saat membuat user baru (userId null), opsional saat edit.
        $fotoRule = $this->userId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'username' => ['required', 'string', 'max:60', Rule::unique('users')->ignore($this->userId)],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users')->ignore($this->userId)],
            'password' => $passwordRule,
            'password_confirmation' => $this->userId ? 'nullable' : 'required_with:password',
            'foto' => $fotoRule,
            'roles_id' => 'required|exists:roles,id',
        ];
    }

    /**
     * Pesan validasi kustom agar admin memahami alasan kegagalan input.
     */
    protected function messages()
    {
        return [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'roles_id.required' => 'Peran (role) wajib dipilih.',
            'foto.required' => 'Foto profil wajib diunggah.', // Pesan khusus saat foto belum diunggah.
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    /**
     * Memuat daftar role yang tersedia untuk dropdown form.
     */
    public function mount()
    {
        $this->roles = Role::all();
    }
    
    /**
     * Reset paginasi ketika kata kunci pencarian berubah.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar user beserta role untuk ditampilkan pada tabel.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $users = User::with('role')
            ->where(function ($query) use ($searchTerm) {
                $query->where('username', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.administrator.dasbor-pengelolaan-pengguna', [
            'users' => $users
        ]);
    }

    /**
     * Membuka modal tambah dengan mereset seluruh field.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi form dengan data user terpilih agar dapat diedit.
     */
  public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->existingFoto = $user->foto ? Storage::url($user->foto) : null;
        $this->roles_id = $user->roles_id;
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan perubahan akun.
     * - Melakukan validasi termasuk larangan mengubah diri sendiri dan superadmin terakhir.
     * - Mengelola unggahan foto baru beserta penghapusan foto lama.
     * - Mengirim SweetAlert sebagai umpan balik ke antarmuka.
     */
    public function store()
    {
        $validatedData = $this->validate();

        if ($this->userId == auth()->id()) {
            $this->dispatch('swal:error', [
                'message' => 'Aksi tidak diizinkan. Anda tidak dapat mengubah data diri sendiri di halaman ini.'
            ]);
            return;
        }

        if ($this->userId) {
            $userToUpdate = User::findOrFail($this->userId);
            if ($userToUpdate->role->name == 'superadmin' && $validatedData['roles_id'] != $userToUpdate->roles_id) {
                $superadminCount = User::whereHas('role', function ($query) {
                    $query->where('name', 'superadmin');
                })->count();

                if ($superadminCount <= 1) {
                    $this->dispatch('swal:error', [
                        'message' => 'Aksi Gagal! Tidak dapat mengubah peran superadmin terakhir.'
                    ]);
                    return;
                }
            }
        }
        
        $data = [
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'roles_id' => $validatedData['roles_id'],
        ];
        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }
        if ($this->foto) {
            if ($this->userId && $this->existingFoto) {
                $oldPath = str_replace('/storage/', '', $this->existingFoto);
                Storage::disk('public')->delete($oldPath);
            }
            $data['foto'] = $this->foto->store('fotos', 'public');
        }
        User::updateOrCreate(['id' => $this->userId], $data);
        
        $this->dispatch('swal:success', [
            'message' => $this->userId ? 'Data user berhasil diperbarui.' : 'User baru berhasil ditambahkan.'
        ]);

        $this->closeModal();
    }

    #[On('destroy')]
    /**
     * Menghapus akun setelah memastikan bukan akun sendiri atau superadmin terakhir,
     * serta menghapus foto dari storage bila tersedia.
     */
    public function destroy($id)
    {
        if ($id == auth()->id()) {
            $this->dispatch('swal:error', [
                'message' => 'Aksi tidak diizinkan. Anda tidak dapat menghapus akun Anda sendiri.'
            ]);
            return;
        }

        $user = User::findOrFail($id);

        if ($user->role->name == 'superadmin') {
            $superadminCount = User::whereHas('role', function ($query) {
                $query->where('name', 'superadmin');
            })->count();

            if ($superadminCount <= 1) {
                $this->dispatch('swal:error', [
                    'message' => 'Aksi Gagal! Tidak dapat menghapus superadmin terakhir.'
                ]);
                return;
            }
        }

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }
        $user->delete();

        $this->dispatch('swal:success', [
            'message' => 'Data user berhasil dihapus.'
        ]);
    }

    /**
     * Menutup modal dan membersihkan input.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mereset seluruh properti dan error validasi agar form siap digunakan ulang.
     */
    private function resetForm()
    {
        $this->reset(['userId', 'username', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto', 'roles_id']);
        $this->resetErrorBag();
    }
}
