<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class UserDashboard extends Component
{
    use WithFileUploads;
    #[Title("Manajemen User")]
    #[Layout("components.layouts.layout-admin-dashboard")]
    // Properti untuk state modal dan form
    public $isModalOpen = false;
    public $userId, $username, $email, $password, $foto, $existingFoto, $roles_id;
    public $roles;

    // Aturan validasi
    protected function rules()
    {
        return [
            'username' => ['required', 'string', 'max:60', Rule::unique('users')->ignore($this->userId)],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'foto' => $this->userId ? 'nullable|image|max:2048' : 'required|image|max:2048', // 2MB Max
            'roles_id' => 'required|exists:roles,id',
        ];
    }

    // Pesan validasi kustom
    protected $messages = [
        'username.required' => 'Username tidak boleh kosong.',
        'email.required' => 'Email tidak boleh kosong.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password tidak boleh kosong.',
        'foto.required' => 'Foto tidak boleh kosong.',
        'foto.image' => 'File harus berupa gambar.',
        'roles_id.required' => 'Role harus dipilih.',
    ];

    // Method yang dipanggil saat komponen pertama kali dimuat
    public function mount()
    {
        $this->roles = Role::all();
    }

    // Method untuk merender view komponen
    public function render()
    {
        return view('livewire.admin.user-dashboard', [
            'users' => User::with('role')->latest()->get()
        ]);
    }

    // Membuka modal untuk membuat user baru
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    // Membuka modal untuk mengedit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->password = ''; // Kosongkan password
        $this->existingFoto = $user->foto ? Storage::url($user->foto) : null;
        $this->roles_id = $user->roles_id;
        $this->isModalOpen = true;
    }

    // Menyimpan data (baik user baru maupun yang diedit)
    public function store()
    {
        $this->validate();

        $data = [
            'username' => $this->username,
            'email' => $this->email,
            'roles_id' => $this->roles_id,
        ];

        // Handle password jika diisi
        if (!empty($this->password)) {
            $data['password'] = $this->password; // Model akan hash otomatis
        }

        // Handle upload foto
        if ($this->foto) {
            // Hapus foto lama jika ada (saat update)
            if ($this->userId && $this->existingFoto) {
                $oldPath = str_replace('/storage/', '', $this->existingFoto);
                Storage::disk('public')->delete($oldPath);
            }
            $data['foto'] = $this->foto->store('fotos', 'public');
        }

        // Update atau Create
        User::updateOrCreate(['id' => $this->userId], $data);
        
        // Kirim notifikasi sukses
        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Sukses!',
            'text' => $this->userId ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.'
        ]);

        $this->closeModal();
    }
    
    // Konfirmasi sebelum menghapus
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'type' => 'warning',
            'title' => 'Anda yakin?',
            'text' => 'Data yang dihapus tidak dapat dikembalikan!',
            'id' => $id
        ]);
    }

    // Menghapus user setelah konfirmasi
    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }
        $user->delete();

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Dihapus!',
            'text' => 'User berhasil dihapus.'
        ]);
    }

    // Menutup modal dan mereset form
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    // Fungsi helper untuk mereset properti form
    private function resetForm()
    {
        $this->userId = null;
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->foto = null;
        $this->existingFoto = null;
        $this->roles_id = '';
        $this->resetErrorBag();
    }
}
