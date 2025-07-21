<?php

namespace App\Livewire\Admin;

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
class GuruDashboard extends Component
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

        return view('livewire.admin.guru-dashboard', ['guruData' => $guruData]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

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
    public function destroy($id)
    {
        $guru = Guru::with('user')->findOrFail($id);
        if ($guru->user && $guru->user->foto) {
            Storage::disk('public')->delete($guru->user->foto);
        }
        if ($guru->user) $guru->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data guru berhasil dihapus.']);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['isModalOpen', 'guruId', 'userId', 'nip_guru', 'nama_guru', 'kontak_guru', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto']);
        $this->resetErrorBag();
    }
}
