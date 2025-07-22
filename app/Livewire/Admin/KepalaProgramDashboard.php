<?php

namespace App\Livewire\Admin;

use App\Models\Jurusan;
use App\Models\KepalaProgram;
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
#[Title("Manajemen Kepala Program")]
class KepalaProgramDashboard extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $isModalOpen = false;
    public $kaprogId, $userId;
    public $nip_kepala_program, $nama_kepala_program, $id_jurusan, $email;
    public $password, $password_confirmation;
    public $foto, $existingFoto;
    public $search = '';
    public $perPage = 10;

    protected function rules()
    {
        $passwordRule = $this->kaprogId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->kaprogId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nip_kepala_program' => ['required', 'numeric', 'digits_between:4,18', Rule::unique('kepala_program', 'nip_kepala_program')->ignore($this->kaprogId, 'nip_kepala_program')],
            'nama_kepala_program' => ['required', 'string', 'max:100'],
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan', Rule::unique('kepala_program', 'id_jurusan')->ignore($this->kaprogId, 'nip_kepala_program')],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $kaprogData = KepalaProgram::with(['user', 'jurusan'])
            ->where('nama_kepala_program', 'like', $searchTerm)
            ->orWhereHas('jurusan', fn($q) => $q->where('nama_jurusan_lengkap', 'like', $searchTerm))
            ->orWhereHas('user', fn($q) => $q->where('email', 'like', $searchTerm))
            ->latest('nip_kepala_program')
            ->paginate($this->perPage);

        $jurusanOptions = Jurusan::orderBy('nama_jurusan_lengkap')->get();

        return view('livewire.admin.kepala-program-dashboard', [
            'kaprogData' => $kaprogData,
            'jurusanOptions' => $jurusanOptions,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $kaprog = KepalaProgram::with('user')->findOrFail($id);
        $this->kaprogId = $kaprog->nip_kepala_program;
        $this->userId = $kaprog->user_id;
        $this->nip_kepala_program = $kaprog->nip_kepala_program;
        $this->nama_kepala_program = $kaprog->nama_kepala_program;
        $this->id_jurusan = $kaprog->id_jurusan;
        if ($kaprog->user) {
            $this->email = $kaprog->user->email;
            $this->existingFoto = $kaprog->user->foto ? Storage::url($kaprog->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->kaprogId) { // Update
                $kaprog = KepalaProgram::findOrFail($this->kaprogId);
                $user = $kaprog->user;
                $userData = ['username' => $validatedData['nama_kepala_program'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) $userData['password'] = Hash::make($validatedData['password']);
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);
                $kaprog->update(['nip_kepala_program' => $validatedData['nip_kepala_program'], 'nama_kepala_program' => $validatedData['nama_kepala_program'], 'id_jurusan' => $validatedData['id_jurusan']]);
            } else { // Create
                $kaprogRole = Role::where('name', 'kepalaprogram')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');
                $newUser = User::create(['roles_id' => $kaprogRole->id, 'username' => $validatedData['nama_kepala_program'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                KepalaProgram::create(['user_id' => $newUser->id, 'nip_kepala_program' => $validatedData['nip_kepala_program'], 'nama_kepala_program' => $validatedData['nama_kepala_program'], 'id_jurusan' => $validatedData['id_jurusan']]);
            }
        });

        $this->dispatch('swal:success', ['message' => $this->kaprogId ? 'Data berhasil diperbarui.' : 'Data baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-kepala-program')]
    public function destroy($id)
    {
        $kaprog = KepalaProgram::with('user')->findOrFail($id);
        if ($kaprog->user && $kaprog->user->foto) Storage::disk('public')->delete($kaprog->user->foto);
        if ($kaprog->user) $kaprog->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data Kepala Program berhasil dihapus.']);
    }

    public function closeModal() { $this->isModalOpen = false; $this->resetForm(); }
    private function resetForm() { $this->reset(); $this->resetErrorBag(); }
}
