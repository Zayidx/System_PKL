<?php

namespace App\Livewire\Administrator;

use App\Models\StaffHubin;
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

#[Layout("components.layouts.layout-staf-hubin-dashboard")]
#[Title("Manajemen Staf Hubin")]
/**
 * Mengelola data staf hubin beserta akun login:
 * - Memungkinkan admin menambah, mengedit, dan menghapus staf.
 * - Menangani unggah foto profil serta penghapusan file lama.
 * - Dapat dikembangkan untuk pengecekan relasi lain sebelum penghapusan.
 */
class DasborStafHubin extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $staffId, $userId;
    public $nip_staff, $nama_staff, $email;
    public $password, $password_confirmation;
    public $foto, $existingFoto;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Validasi dinamis agar identitas dan akun staf tetap konsisten.
     */
    protected function rules()
    {
        $passwordRule = $this->staffId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->staffId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nip_staff' => ['required', 'numeric', 'digits_between:4,18', Rule::unique('staff_hubin', 'nip_staff')->ignore($this->staffId, 'nip_staff')],
            'nama_staff' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    /**
     * Pesan validasi kustom untuk mempermudah memahami error input.
     */
    protected function messages()
    {
        return [
            'nip_staff.required' => 'NIP/ID Staff wajib diisi.',
            'nip_staff.unique' => 'NIP/ID ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nama_staff.required' => 'Nama staff wajib diisi.',
            'password.required' => 'Password wajib diisi untuk akun baru.',
            'foto.required' => 'Foto profil wajib diunggah untuk akun baru.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar staf hubin dan mengirimkannya ke tampilan Livewire.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $staffData = StaffHubin::with('user')
            ->where('nama_staff', 'like', $searchTerm)
            ->orWhere('nip_staff', 'like', $searchTerm)
            ->orWhereHas('user', fn($q) => $q->where('email', 'like', $searchTerm))
            ->latest('nip_staff')
            ->paginate($this->perPage);

        return view('livewire.administrator.dasbor-staf-hubin', ['staffData' => $staffData]);
    }

    /**
     * Menampilkan modal tambah dalam keadaan bersih.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi form dengan data staf yang dipilih agar bisa diperbarui.
     */
    public function edit($id)
    {
        $staff = StaffHubin::with('user')->findOrFail($id);
        $this->staffId = $staff->nip_staff;
        $this->userId = $staff->user_id;
        $this->nip_staff = $staff->nip_staff;
        $this->nama_staff = $staff->nama_staff;
        
        if ($staff->user) {
            $this->email = $staff->user->email;
            $this->existingFoto = $staff->user->foto ? Storage::url($staff->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan data staf melalui transaksi database agar sinkron antara
     * tabel user dan staf hubin. Termasuk mengganti foto profil jika diunggah.
     * Setelah sukses, event SweetAlert dikirim dan modal ditutup.
     */
    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->staffId) { // Update
                $staff = StaffHubin::findOrFail($this->staffId);
                $user = $staff->user;

                $userData = ['username' => $validatedData['nama_staff'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) $userData['password'] = Hash::make($validatedData['password']);
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);
                $staff->update(['nip_staff' => $validatedData['nip_staff'], 'nama_staff' => $validatedData['nama_staff']]);

            } else { // Create
                $staffRole = Role::where('name', 'staffhubin')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');

                $newUser = User::create(['roles_id' => $staffRole->id, 'username' => $validatedData['nama_staff'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                StaffHubin::create(['nip_staff' => $validatedData['nip_staff'], 'user_id' => $newUser->id, 'nama_staff' => $validatedData['nama_staff']]);
            }
        });

        $this->dispatch('swal:success', ['message' => $this->staffId ? 'Data staff berhasil diperbarui.' : 'Staff baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-staff-hubin')]
    /**
     * Menghapus staf hubin beserta akun dan foto profilnya.
     * Tambahkan pengecekan relasi lain jika diperlukan.
     */
    public function destroy($id)
    {
        // Anda bisa menambahkan pengecekan relasi di sini jika diperlukan.
        $staff = StaffHubin::with('user')->findOrFail($id);

        if ($staff->user && $staff->user->foto) Storage::disk('public')->delete($staff->user->foto);
        if ($staff->user) $staff->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data staff berhasil dihapus.']);
    }

    /**
     * Menutup modal dan mereset input serta error.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Membersihkan seluruh properti formulir untuk penggunaan ulang.
     */
    private function resetForm()
    {
        $this->reset(['isModalOpen', 'staffId', 'userId', 'nip_staff', 'nama_staff', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto']);
        $this->resetErrorBag();
    }
}
