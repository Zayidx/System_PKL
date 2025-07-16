<?php

namespace App\Livewire\Auth;

use App\Models\User;              // Mengimpor model User untuk menyimpan data pengguna
use Illuminate\Support\Facades\Auth;    // Mengimpor Auth untuk proses login
use Illuminate\Support\Facades\Crypt;   // Mengimpor Crypt (meskipun belum digunakan di sini)
use Illuminate\Support\Facades\Hash;    // Mengimpor Hash untuk mengenkripsi password
use Livewire\Component;                  // Mengimpor kelas utama Livewire

class Register extends Component
{
    // Properti publik untuk menyimpan input pengguna
    public $email, $username, $password;    // Variabel untuk email, username, dan password
    public $isSubmitActive = false;         // Variabel untuk mengatur status tombol submit (aktif/tidak)

    /**
     * Aturan validasi untuk setiap input
     * - email: minimal 8 karakter, maksimal 16 karakter
     * - username: minimal 4 karakter, maksimal 100 karakter
     * - password: minimal 6 karakter
     */
    protected $rules = [
        'email' => 'min:8|max:16',
        'username' => 'min:4|max:100',
        'password' => 'min:6',
    ];

    /**
     * Pesan kustom untuk validasi
     * - Memberikan penjelasan yang ramah jika aturan tidak terpenuhi
     */
    protected $messages = [
        'email.min' => 'Minimal 8 huruf',
        'email.max' => 'Maksimal 16 huruf',
        'username.min' => 'Minimal 6 huruf',   // Perhatian: ada inkonsistensi dengan rules (seharusnya 4)
        'username.max' => 'Minimal 16 huruf',  // Perhatian: ada inkonsistensi dengan rules (seharusnya maksimal)
        'password.min' => 'Minimal 6 karakter',
    ];

    /**
     * Memeriksa apakah semua form sudah diisi
     * Mengaktifkan tombol submit jika semua field (email, username, password) tidak kosong
     */
    public function isFormFilled()
    {
        if (!empty($this->email) && !empty($this->username) && !empty($this->password)) {
            $this->isSubmitActive = true;
        } else {
            $this->isSubmitActive = false;
        }
    }

    /**
     * Fungsi yang dipanggil saat ada perubahan pada input
     * - Memperbarui status tombol submit
     * - Melakukan validasi hanya pada field yang diubah
     */
    public function updated($property)
    {
        $this->isFormFilled();  // Periksa ulang apakah form sudah lengkap

        if ($property == 'email') {
            $this->validateOnly('email');  // Validasi hanya untuk email
        }

        // if ($property == 'username') {
        //     $this->validateOnly('username');  // Bagian ini dikomentari, jadi tidak divalidasi secara real-time
        // }

        if ($property == 'password') {
            $this->validateOnly('password');  // Validasi hanya untuk password
        }
    }

    /**
     * Proses pendaftaran pengguna
     * - Membuat pengguna baru di database dengan role ID 6
     * - Mengenkripsi password menggunakan Hash
     * - Melakukan login otomatis setelah registrasi
     * - Mengarahkan ke dashboard santri
     */
    public function register()
    {
        $user = User::create([
            'roles_id' => 6,         // Menetapkan role ID (misalnya untuk santri)
            'name' => $this->username,  // Menggunakan username sebagai nama
            'email' => $this->email,    // Menyimpan email
            'password' => Hash::make($this->password),  // Mengenkripsi password
        ]);

        request()->session()->regenerate();  // Memperbarui sesi untuk keamanan
        Auth::login($user);                  // Login pengguna secara otomatis
        return to_route('santri.dashboard'); // Alihkan ke dashboard santri
    }

    /**
     * Mengembalikan tampilan registrasi
     * - Menggunakan view 'livewire.auth.register' dengan layout 'components.layouts.auth'
     */
    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.auth');
    }
}