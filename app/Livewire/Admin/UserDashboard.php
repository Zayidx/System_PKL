<?php
// =========================================================================================
// FILE LOGIC LIVEWIRE: Komponen UserDashboard (Versi Perbaikan Lanjutan)
// Path: app/Livewire/Admin/UserDashboard.php
// ANALISIS PERUBAHAN:
// 1. [PERBAIKAN] Aturan validasi untuk 'foto' diubah menjadi kondisional.
//    - 'required' saat membuat user baru (untuk mengatasi error SQL).
//    - 'nullable' saat mengedit user yang sudah ada.
// 2. [PERBAIKAN] Menambahkan pesan validasi untuk 'foto.required'.
// =========================================================================================

namespace App\Livewire\Admin;

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
class UserDashboard extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';
    #[Title("Manajemen User")]

    public $isModalOpen = false;
    public $userId, $username, $email, $password, $password_confirmation, $foto, $existingFoto;
    
    public $roles_id = null;

    public $roles;
    public $search = '';
    public $perPage = 10;

    protected function rules()
    {
        $passwordRule = $this->userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed';
        
        // [PERBAIKAN] Foto wajib diisi saat membuat user baru (userId null), opsional saat edit.
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
            'foto.required' => 'Foto profil wajib diunggah.', // [PERBAIKAN] Pesan untuk foto wajib diisi.
            'foto.image' => 'File harus berupa gambar.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    public function mount()
    {
        $this->roles = Role::all();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

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

        return view('livewire.admin.user-dashboard', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

/*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
/*******  aedcc3f4-77c3-4ee2-b6f4-882204c9672c    *******/  public function edit($id)
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

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['userId', 'username', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto', 'roles_id']);
        $this->resetErrorBag();
    }
}
