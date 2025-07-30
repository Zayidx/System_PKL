<?php

namespace App\Livewire\Admin;

use App\Models\PembimbingSekolah;
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
#[Title("Manajemen Pembimbing Sekolah")]
class PembimbingSekolahDashboard extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $pembimbingId, $userId;
    public $nip_pembimbing_sekolah, $nama_pembimbing_sekolah, $email;
    public $kontak_pembimbing_sekolah;
    public $password, $password_confirmation;
    public $foto, $existingFoto;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    protected function rules()
    {
        $passwordRule = $this->pembimbingId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->pembimbingId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nip_pembimbing_sekolah' => ['required', 'numeric', 'digits_between:4,18', Rule::unique('pembimbing_sekolah', 'nip_pembimbing_sekolah')->ignore($this->pembimbingId, 'nip_pembimbing_sekolah')],
            'nama_pembimbing_sekolah' => ['required', 'string', 'max:100'],
            'kontak_pembimbing_sekolah' => ['nullable', 'string', 'max:17'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    protected function messages()
    {
        return [
            'nip_pembimbing_sekolah.required' => 'NIP/ID Pembimbing wajib diisi.',
            'nip_pembimbing_sekolah.unique' => 'NIP/ID ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nama_pembimbing_sekolah.required' => 'Nama pembimbing wajib diisi.',
            'kontak_pembimbing_sekolah.max' => 'Nomor kontak maksimal 17 karakter.',
            'password.required' => 'Password wajib diisi untuk akun baru.',
            'foto.required' => 'Foto profil wajib diunggah untuk akun baru.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $pembimbingData = PembimbingSekolah::with(['user', 'perusahaan'])
            ->where('nama_pembimbing_sekolah', 'like', $searchTerm)
            ->orWhere('nip_pembimbing_sekolah', 'like', $searchTerm)
            ->orWhere('kontak_pembimbing_sekolah', 'like', $searchTerm)
            ->orWhereHas('perusahaan', fn($q) => $q->where('nama_perusahaan', 'like', $searchTerm))
            ->orWhereHas('user', fn($q) => $q->where('email', 'like', $searchTerm))
            ->latest('nip_pembimbing_sekolah')
            ->paginate($this->perPage);

        return view('livewire.admin.pembimbing-sekolah-dashboard', ['pembimbingData' => $pembimbingData]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $pembimbing = PembimbingSekolah::with(['user', 'perusahaan'])->findOrFail($id);
        $this->pembimbingId = $pembimbing->nip_pembimbing_sekolah;
        $this->userId = $pembimbing->user_id;
        $this->nip_pembimbing_sekolah = $pembimbing->nip_pembimbing_sekolah;
        $this->nama_pembimbing_sekolah = $pembimbing->nama_pembimbing_sekolah;
        $this->kontak_pembimbing_sekolah = $pembimbing->kontak_pembimbing_sekolah;
        
        if ($pembimbing->user) {
            $this->email = $pembimbing->user->email;
            $this->existingFoto = $pembimbing->user->foto ? Storage::url($pembimbing->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->pembimbingId) { // Update
                $pembimbing = PembimbingSekolah::findOrFail($this->pembimbingId);
                $user = $pembimbing->user;

                $userData = ['username' => $validatedData['nama_pembimbing_sekolah'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) $userData['password'] = Hash::make($validatedData['password']);
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);
                $pembimbing->update([
                    'nip_pembimbing_sekolah' => $validatedData['nip_pembimbing_sekolah'], 
                    'nama_pembimbing_sekolah' => $validatedData['nama_pembimbing_sekolah'],
                    'kontak_pembimbing_sekolah' => $validatedData['kontak_pembimbing_sekolah'],
                ]);

            } else { // Create
                $pembimbingRole = Role::where('name', 'pembimbingsekolah')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');

                $newUser = User::create(['roles_id' => $pembimbingRole->id, 'username' => $validatedData['nama_pembimbing_sekolah'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                PembimbingSekolah::create([
                    'nip_pembimbing_sekolah' => $validatedData['nip_pembimbing_sekolah'], 
                    'user_id' => $newUser->id, 
                    'nama_pembimbing_sekolah' => $validatedData['nama_pembimbing_sekolah'],
                    'kontak_pembimbing_sekolah' => $validatedData['kontak_pembimbing_sekolah'],
                ]);
            }
        });

        $this->dispatch('swal:success', ['message' => $this->pembimbingId ? 'Data pembimbing berhasil diperbarui.' : 'Pembimbing baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-pembimbing-sekolah')]
    public function destroy($id)
    {
        // Anda bisa menambahkan pengecekan relasi di sini jika diperlukan,
        // contohnya: `if ($pembimbing->prakerin()->exists()) { ... }`
        $pembimbing = PembimbingSekolah::with('user')->findOrFail($id);

        if ($pembimbing->user && $pembimbing->user->foto) Storage::disk('public')->delete($pembimbing->user->foto);
        if ($pembimbing->user) $pembimbing->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data pembimbing berhasil dihapus.']);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['isModalOpen', 'pembimbingId', 'userId', 'nip_pembimbing_sekolah', 'nama_pembimbing_sekolah', 'kontak_pembimbing_sekolah', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto']);
        $this->resetErrorBag();
    }
}
