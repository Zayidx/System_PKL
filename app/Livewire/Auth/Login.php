<?php

namespace App\Livewire\Auth;

use App\Mail\LoginOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;

    public $otp;
    public $showOtpForm = false;

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Cek apakah sudah di dashboard, jika belum redirect
            if (request()->route()->getName() !== 'admin.dashboard' && $user->role && $user->role->name === 'superadmin') {
                return $this->redirect(route('admin.dashboard'), navigate: true);
            } elseif (request()->route()->getName() !== 'user.dashboard' && $user->role && $user->role->name === 'user') {
                return $this->redirect(route('user.dashboard'), navigate: true);
            }
        }
    }

    #[Title('Halaman Login')]
    #[Layout('components.layouts.layout-auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }

    public function attemptLogin()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::validate(['email' => $this->email, 'password' => $this->password])) {
            $this->sendOtp();
        } else {
            $this->addError('credentials', 'Gagal masuk, email atau password salah!');
        }
    }

    public function sendOtp()
    {
        $otpCode = rand(100000, 999999);
        $redisKey = "otp:" . $this->email;
        Redis::setex($redisKey, 300, $otpCode); // OTP berlaku 5 menit

        try {
            Mail::to($this->email)->send(new LoginOtpMail($otpCode));
            $this->showOtpForm = true;
            $this->reset('password');
            $this->dispatch('otp-sent'); // Kirim event ke frontend
        } catch (\Exception $e) {
            $this->addError('credentials', 'Gagal mengirim OTP. Pastikan konfigurasi email benar.');
            report($e);
        }
    }

    public function resendOtp()
    {
        $this->sendOtp();
    }

    public function verifyOtpAndLogin()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);
        $redisKey = "otp:" . $this->email;
        $storedOtp = Redis::get($redisKey);

        if ($storedOtp && $storedOtp == $this->otp) {
            $user = User::where('email', $this->email)->first();
            if ($user) {
                Auth::login($user);
                Redis::del($redisKey);
                request()->session()->regenerate();

                // Logika pengalihan berdasarkan role pengguna
                if ($user->role && $user->role->name === 'superadmin') {
                    return $this->redirect(route('admin.dashboard'), navigate: true);
                } elseif ($user->role && $user->role->name === 'user') {
                    return $this->redirect(route('user.dashboard'), navigate: true);
                } else {
                    Auth::logout();
                    return $this->redirect(route('login'));
                }
            } else {
                $this->addError('credentials', 'User tidak ditemukan.');
                return;
            }
        }
        $this->addError('otp', 'Kode OTP tidak valid atau telah kedaluwarsa.');
    }

    public function cancelOtp()
    {
        $this->reset('otp', 'showOtpForm', 'email', 'password');
        $this->resetErrorBag();
    }

    /**
     * [PERBAIKAN] Menambahkan kembali fungsi logout.
     * Fungsi ini akan dipanggil oleh rute /logout.
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }
}
