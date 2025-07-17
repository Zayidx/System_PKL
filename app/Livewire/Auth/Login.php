<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Livewire\Component;
// use Detection\MobileDetect; // Dihapus jika tidak digunakan di logic lain

class Login extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;

    #[Title('Halaman Login')]
    #[Layout('components.layouts.layout-auth')]

    // public $is_mobile; // Dihapus jika tidak digunakan di logic lain

    // public function mount()
    // {
    //     $mobile = new MobileDetect();
    //     $this->is_mobile = $mobile->isMobile();
    // }

    public function login()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        // Coba untuk melakukan otentikasi
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan (mencegah session fixation)
            request()->session()->regenerate();

            // Ambil user yang sedang login
            $user = Auth::user();

            // --- BAGIAN YANG DIPERBAIKI ---
            // Gunakan relasi Eloquent yang sudah didefinisikan di model User.
            // Ini lebih efisien karena tidak perlu query baru jika relasi sudah di-load.
            // Pastikan kolom di tabel 'roles' adalah 'name' (bukan 'nama').
            if ($user->role && $user->role->name === 'superadmin') {
                // Jika role adalah 'superadmin', redirect ke dashboard admin
                return $this->redirect(route('admin.dashboard'), navigate: true);
            } else {
                // Untuk semua role lain (termasuk 'user'), redirect ke dashboard user
                return $this->redirect(route('admin.master-admin.user'), navigate: true);
            }
        }

        // Jika otentikasi gagal, tambahkan pesan error
        $this->addError('credentials', 'Gagal masuk, email atau password salah!');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
