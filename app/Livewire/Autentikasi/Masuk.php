<?php

// app/Livewire/Autentikasi/Masuk.php

namespace App\Livewire\Autentikasi;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Menangani proses autentikasi masuk:
 * - Memvalidasi kredensial dan mengarahkan pengguna ke dasbor sesuai peran.
 * - Menyiapkan pesan sukses (SweetAlert) setelah login.
 * - Mengamankan akses dengan memaksa logout bila role tidak dikenal.
 */
class Masuk extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;

    /**
     * Cek jika user sudah login saat komponen di-mount.
     * Jika sudah, redirect ke dashboard sesuai role.
     */
    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role) {
                $roleName = $user->role->name;
                
                if ($roleName === 'admin' || $roleName === 'superadmin') {
                   return $this->redirect(route('administrator.dasbor'), navigate: true);
                } elseif ($roleName === 'staffhubin') {
                   return $this->redirect(route('staf-hubin.dasbor'), navigate: true);
                } elseif ($roleName === 'user') {
                    return $this->redirect(route('pengguna.dasbor'), navigate: true);
                }
            }
            
            // Jika user login tapi tidak punya role yang valid, logout paksa.
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }

    #[Title('Halaman Masuk')]
    #[Layout('components.layouts.layout-auth')]
    /**
     * Menyajikan tampilan form masuk Livewire.
     */
    public function render()
    {
        return view('livewire.autentikasi.masuk');
    }

    /**
     * Mencoba untuk login user.
     * Jika berhasil, langsung redirect ke dashboard.
     */
    public function attemptLogin()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = ['email' => $this->email, 'password' => $this->password];
        $user = User::where('email', $this->email)->first();

        // Cek kredensial dan pastikan user memiliki role yang diizinkan.
        if ($user && Auth::validate($credentials)) {
            if ($user->role && in_array($user->role->name, ['admin', 'superadmin', 'user', 'staffhubin'])) {
                
                // Langsung login user
                Auth::login($user);
                request()->session()->regenerate();

                // Set session untuk sweet alert
                $roleName = $user->role->name;
                $roleDisplayName = $this->getRoleDisplayName($roleName);
                request()->session()->flash('login_success', [
                    'message' => "Berhasil login sebagai $roleDisplayName",
                    'role' => $roleName
                ]);

                // Redirect berdasarkan role
                if ($roleName === 'admin' || $roleName === 'superadmin') {
                    return $this->redirect(route('administrator.dasbor'), navigate: true);
                } elseif ($roleName === 'staffhubin') {
                    return $this->redirect(route('staf-hubin.dasbor'), navigate: true);
                } elseif ($roleName === 'user') {
                    return $this->redirect(route('pengguna.dasbor'), navigate: true);
                }
            } else {
                $this->addError('credentials', 'Anda tidak memiliki hak akses untuk masuk.');
            }
        } else {
            $this->addError('credentials', 'Gagal masuk, email atau password salah!');
        }
    }

    /**
     * Get display name untuk role
     */
    private function getRoleDisplayName($roleName)
    {
        return match($roleName) {
            'admin', 'superadmin' => 'Administrator',
            'staffhubin' => 'Staf Hubin',
            'user' => 'Siswa',
            default => 'User'
        };
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }
}
