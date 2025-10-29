<?php

namespace App\Livewire\Administrator;

use App\Models\PembimbingPerusahaan;
use App\Models\Perusahaan;
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
#[Title("Manajemen Pembimbing Perusahaan")]
/**
 * Fasilitas admin untuk mengelola pembimbing dari sisi perusahaan:
 * - Menautkan pembimbing dengan akun login dan perusahaan mitra.
 * - Mengelola unggahan foto profil dan memastikan file lama dihapus ketika diganti.
 * - Memastikan data hanya dihapus ketika tidak ada relasi prakerin atau penilaian yang aktif.
 */
class DasborPembimbingPerusahaan extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $pembimbingId, $userId;
    public $id_perusahaan, $nama, $no_hp, $email;
    public $password, $password_confirmation;
    public $foto, $existingFoto;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Validasi agar data pembimbing dan akun pengguna konsisten.
     */
    protected function rules()
    {
        $passwordRule = $this->pembimbingId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->pembimbingId ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'id_perusahaan' => ['required', 'exists:perusahaan,id_perusahaan'],
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'numeric', 'digits_between:10,15'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    /**
     * Pesan validasi khusus untuk mempermudah pengguna memahami kesalahan input.
     */
    protected function messages()
    {
        return [
            'id_perusahaan.required' => 'Perusahaan wajib dipilih.',
            'id_perusahaan.exists' => 'Perusahaan yang dipilih tidak valid.',
            'nama.required' => 'Nama pembimbing wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.required' => 'Password wajib diisi untuk akun baru.',
            'foto.required' => 'Foto profil wajib diunggah untuk akun baru.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Mengambil daftar pembimbing berikut perusahaan & akun user agar tabel kaya informasi.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $pembimbingData = PembimbingPerusahaan::with(['user', 'perusahaan'])
            ->where('nama', 'like', $searchTerm)
            ->orWhereHas('user', fn($q) => $q->where('email', 'like', $searchTerm))
            ->orWhereHas('perusahaan', fn($q) => $q->where('nama_perusahaan', 'like', $searchTerm))
            ->latest('id_pembimbing')
            ->paginate($this->perPage);

        $perusahaanOptions = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('livewire.administrator.dasbor-pembimbing-perusahaan', [
            'pembimbingData' => $pembimbingData,
            'perusahaanOptions' => $perusahaanOptions,
        ]);
    }

    /**
     * Menyiapkan modal penambahan pembimbing dengan mengosongkan seluruh field.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi form dengan data pembimbing yang dipilih untuk proses sunting.
     */
    public function edit($id)
    {
        $pembimbing = PembimbingPerusahaan::with('user')->findOrFail($id);
        $this->pembimbingId = $pembimbing->id_pembimbing;
        $this->userId = $pembimbing->user_id;
        $this->id_perusahaan = $pembimbing->perusahaan->first()->id_perusahaan ?? null;
        $this->nama = $pembimbing->nama;
        $this->no_hp = $pembimbing->no_hp;
        
        if ($pembimbing->user) {
            $this->email = $pembimbing->user->email;
            $this->existingFoto = $pembimbing->user->foto ? Storage::url($pembimbing->user->foto) : null;
        }
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan pembimbing melalui `DB::transaction` agar perubahan user
     * dan pembimbing terjadi bersamaan:
     * - Mode edit memperbarui akun user (password/foto opsional) serta
     *   menyinkronkan perusahaan yang dibina.
     * - Mode tambah membuat akun user baru dengan role pembimbing perusahaan,
     *   mengunggah foto, lalu menautkan ke perusahaan pilihan.
     * Setelah sukses, SweetAlert dikirim dan modal ditutup.
     */
    public function store()
    {
        $validatedData = $this->validate();

        DB::transaction(function () use ($validatedData) {
            if ($this->pembimbingId) { // Update
                $pembimbing = PembimbingPerusahaan::findOrFail($this->pembimbingId);
                $user = $pembimbing->user;

                $userData = ['username' => $validatedData['nama'], 'email' => $validatedData['email']];
                if (!empty($validatedData['password'])) $userData['password'] = Hash::make($validatedData['password']);
                if ($this->foto) {
                    if ($user->foto) Storage::disk('public')->delete($user->foto);
                    $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                }
                $user->update($userData);
                $pembimbing->update(['nama' => $validatedData['nama'], 'no_hp' => $validatedData['no_hp']]);

                // Update perusahaan assignment
                $perusahaan = Perusahaan::find($validatedData['id_perusahaan']);
                if ($perusahaan) {
                    $perusahaan->update(['id_pembimbing_perusahaan' => $pembimbing->id_pembimbing]);
                }

            } else { // Create
                $pembimbingRole = Role::where('name', 'pembimbingperusahaan')->firstOrFail();
                $fotoPath = $this->foto->store('fotos/profil', 'public');

                $newUser = User::create(['roles_id' => $pembimbingRole->id, 'username' => $validatedData['nama'], 'email' => $validatedData['email'], 'password' => Hash::make($validatedData['password']), 'foto' => $fotoPath]);
                $pembimbing = PembimbingPerusahaan::create(['user_id' => $newUser->id, 'nama' => $validatedData['nama'], 'no_hp' => $validatedData['no_hp']]);

                // Assign pembimbing to perusahaan
                $perusahaan = Perusahaan::find($validatedData['id_perusahaan']);
                if ($perusahaan) {
                    $perusahaan->update(['id_pembimbing_perusahaan' => $pembimbing->id_pembimbing]);
                }
            }
        });

        $this->dispatch('swal:success', ['message' => $this->pembimbingId ? 'Data pembimbing berhasil diperbarui.' : 'Pembimbing baru berhasil ditambahkan.']);
        $this->closeModal();
    }

    #[On('destroy-pembimbing')]
    /**
     * Menghapus pembimbing setelah memastikan tidak ada relasi prakerin/penilaian.
     * Termasuk membersihkan akun user dan foto profilnya.
     */
    public function destroy($id)
    {
        $pembimbing = PembimbingPerusahaan::with(['user', 'prakerin', 'penilaian'])->findOrFail($id);

        if ($pembimbing->prakerin()->exists() || $pembimbing->penilaian()->exists()) {
            $this->dispatch('swal:error', ['message' => 'Gagal! Pembimbing ini tidak dapat dihapus karena terkait dengan data Prakerin atau Penilaian.']);
            return;
        }

        if ($pembimbing->user && $pembimbing->user->foto) Storage::disk('public')->delete($pembimbing->user->foto);
        if ($pembimbing->user) $pembimbing->user->delete();
        
        $this->dispatch('swal:success', ['message' => 'Data pembimbing berhasil dihapus.']);
    }

    /**
     * Menutup modal dan mereset nilai form.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mengembalikan seluruh properti form ke kondisi awal dan menghapus pesan error.
     */
    private function resetForm()
    {
        $this->reset(['isModalOpen', 'pembimbingId', 'userId', 'id_perusahaan', 'nama', 'no_hp', 'email', 'password', 'password_confirmation', 'foto', 'existingFoto']);
        $this->resetErrorBag();
    }
}
