<?php

namespace App\Livewire\Administrator;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Jurusan;
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
#[Title("Manajemen Siswa")]
/**
 * Modul admin untuk mengelola data siswa dan akun login:
 * - Memungkinkan pencarian, paginasi, serta pemilihan kelas/jurusan.
 * - Menggabungkan operasi CRUD siswa dengan sinkronisasi ke tabel user.
 * - Mengelola foto profil serta memastikan data terkait terhapus dengan aman.
 */
class DasborSiswa extends Component
{
    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal dan Form
    public $isModalOpen = false;
    public $siswaNis, $userId; // ID untuk siswa (NIS) dan user
    public $nis, $nama_siswa, $email, $kontak_siswa, $tempat_lahir, $tanggal_lahir, $id_kelas, $id_jurusan;
    public $password, $password_confirmation;
    public $foto, $existingFoto;

    // Properti untuk fungsionalitas tabel
    public $search = '';
    public $perPage = 10;

    /**
     * Aturan validasi dinamis.
     */
    protected function rules()
    {
        $userIdForUniqueEmail = $this->userId;

        // Aturan validasi untuk password dan foto.
        $passwordRule = $this->siswaNis ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed';
        $fotoRule = $this->siswaNis ? 'nullable|image|max:2048' : 'required|image|max:2048';

        return [
            'nis' => ['required', 'numeric', 'digits_between:8,12', Rule::unique('siswa', 'nis')->ignore($this->siswaNis, 'nis')],
            'nama_siswa' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($userIdForUniqueEmail)],
            'kontak_siswa' => ['required', 'numeric', 'digits_between:10,15'],
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'id_kelas' => ['required', 'exists:kelas,id_kelas'],
            'id_jurusan' => ['required', 'exists:jurusan,id_jurusan'],
            'password' => $passwordRule,
            'foto' => $fotoRule,
        ];
    }

    /**
     * Pesan validasi kustom.
     */
    protected function messages()
    {
        return [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nama_siswa.required' => 'Nama siswa wajib diisi.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_jurusan.required' => 'Jurusan wajib dipilih.',
            'password.required' => 'Password wajib diisi untuk siswa baru.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'foto.required' => 'Foto profil wajib diunggah untuk siswa baru.',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Render komponen dengan data siswa, kelas, dan jurusan.
     */
    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $siswaData = Siswa::with(['user', 'kelas', 'jurusan'])
            ->where('nama_siswa', 'like', $searchTerm)
            ->orWhere('nis', 'like', $searchTerm)
            ->orWhereHas('user', function ($query) use ($searchTerm) {
                $query->where('email', 'like', $searchTerm);
            })
            ->latest('nis')
            ->paginate($this->perPage);

        // Ambil data untuk dropdown di modal
        $kelasOptions = Kelas::orderBy('nama_kelas')->get();
        $jurusanOptions = Jurusan::orderBy('nama_jurusan_lengkap')->get();

        return view('livewire.administrator.dasbor-siswa', [
            'siswaData' => $siswaData,
            'kelasOptions' => $kelasOptions,
            'jurusanOptions' => $jurusanOptions,
        ]);
    }

    /**
     * Membuka modal input dalam kondisi bersih untuk menambahkan siswa baru.
     */
    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    /**
     * Mengisi form dengan data siswa berdasarkan NIS untuk proses penyuntingan.
     */
    public function edit($nis)
    {
        $siswa = Siswa::with('user')->where('nis', $nis)->firstOrFail();
        
        $this->siswaNis = $siswa->nis;
        $this->userId = $siswa->user_id;
        
        $this->nis = $siswa->nis;
        $this->nama_siswa = $siswa->nama_siswa;
        $this->tempat_lahir = $siswa->tempat_lahir;
        $this->tanggal_lahir = $siswa->tanggal_lahir;
        $this->kontak_siswa = $siswa->kontak_siswa;
        $this->id_kelas = $siswa->id_kelas;
        $this->id_jurusan = $siswa->id_jurusan;

        if ($siswa->user) {
            $this->email = $siswa->user->email;
            $this->existingFoto = $siswa->user->foto ? Storage::url($siswa->user->foto) : null;
        }
        
        $this->isModalOpen = true;
    }

    /**
     * Menyimpan data siswa baru atau memperbarui data yang ada.
     */
    public function store()
    {
        $validatedData = $this->validate();

        try {
            DB::transaction(function () use ($validatedData) {
                // Logika untuk UPDATE (mengedit siswa yang sudah ada)
                if ($this->siswaNis) {
                    $siswa = Siswa::with('user')->where('nis', $this->siswaNis)->firstOrFail();
                    $user = $siswa->user;

                    // Update data di tabel 'users'
                    $userData = [
                        'username' => $validatedData['nama_siswa'],
                        'email' => $validatedData['email'],
                    ];
                    if (!empty($validatedData['password'])) {
                        $userData['password'] = Hash::make($validatedData['password']);
                    }
                    if ($this->foto) {
                        if ($user->foto) {
                            Storage::disk('public')->delete($user->foto);
                        }
                        $userData['foto'] = $this->foto->store('fotos/profil', 'public');
                    }
                    $user->update($userData);

                    // Update data di tabel 'siswa'
                    $siswa->update([
                        'nis' => $validatedData['nis'],
                        'nama_siswa' => $validatedData['nama_siswa'],
                        'tempat_lahir' => $validatedData['tempat_lahir'],
                        'tanggal_lahir' => $validatedData['tanggal_lahir'],
                        'kontak_siswa' => $validatedData['kontak_siswa'],
                        'id_kelas' => $validatedData['id_kelas'],
                        'id_jurusan' => $validatedData['id_jurusan'],
                    ]);
                } 
                // Logika untuk CREATE (membuat siswa baru)
                else {
                    $userRole = Role::where('name', 'user')->firstOrFail();
                    $fotoPath = $this->foto->store('fotos/profil', 'public');

                    // Buat User baru
                    $newUser = User::create([
                        'roles_id' => $userRole->id,
                        'username' => $validatedData['nama_siswa'],
                        'email' => $validatedData['email'],
                        'password' => Hash::make($validatedData['password']),
                        'foto' => $fotoPath,
                    ]);

                    // Buat Siswa baru yang terhubung dengan User
                    Siswa::create([
                        'nis' => $validatedData['nis'],
                        'user_id' => $newUser->id,
                        'id_kelas' => $validatedData['id_kelas'],
                        'id_jurusan' => $validatedData['id_jurusan'],
                        'nama_siswa' => $validatedData['nama_siswa'],
                        'tempat_lahir' => $validatedData['tempat_lahir'],
                        'tanggal_lahir' => $validatedData['tanggal_lahir'],
                        'kontak_siswa' => $validatedData['kontak_siswa'],
                    ]);
                }
            });

            $this->dispatch('swal:success', [
                'message' => $this->siswaNis ? 'Data siswa berhasil diperbarui.' : 'Siswa baru berhasil ditambahkan.'
            ]);
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    #[On('destroy-siswa')]
    /**
     * Menghapus siswa sekaligus akun user yang terhubung, termasuk file foto.
     */
    public function destroy($nis)
    {
        try {
            $siswa = Siswa::with('user')->where('nis', $nis)->firstOrFail();
            
            // Hapus foto dari storage jika ada
            if ($siswa->user && $siswa->user->foto) {
                Storage::disk('public')->delete($siswa->user->foto);
            }

            // Hapus data user, yang akan otomatis menghapus data siswa karena onDelete('cascade')
            if ($siswa->user) {
                $siswa->user->delete();
            } else {
                // Jika tidak ada user terkait, hapus siswa saja
                $siswa->delete();
            }

            $this->dispatch('swal:success', ['message' => 'Data siswa berhasil dihapus.']);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Menutup modal dan membersihkan input serta error validasi.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    /**
     * Mengembalikan semua properti form ke nilai awal agar modal siap digunakan ulang.
     */
    private function resetForm()
    {
        $this->reset([
            'isModalOpen', 'siswaNis', 'userId', 'nis', 'nama_siswa', 'email', 
            'kontak_siswa', 'tempat_lahir', 'tanggal_lahir', 'id_kelas', 'id_jurusan',
            'password', 'password_confirmation', 'foto', 'existingFoto'
        ]);
        $this->resetErrorBag();
    }
}
