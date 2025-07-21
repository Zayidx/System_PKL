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
            if ($user->role) {
                $roleName = $user->role->name;
                // PERUBAHAN: Arahkan 'admin' dan 'superadmin' ke dashboard admin.
                if ($roleName === 'admin' || $roleName === 'superadmin') {
                    return $this->redirect(route('admin.dashboard'), navigate: true);
                } 
                // PERUBAHAN: Arahkan 'user' ke dashboard user.
                elseif ($roleName === 'user') {
                    return $this->redirect(route('user.dashboard'), navigate: true);
                }
            }
            
            // Jika user sudah login tapi rolenya tidak sesuai (atau tidak punya role),
            // maka logout paksa dan kembalikan ke halaman login.
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
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

        $credentials = ['email' => $this->email, 'password' => $this->password];
        $user = User::where('email', $this->email)->first();

        // PERUBAHAN: Cek kredensial DAN pastikan user memiliki role yang diizinkan.
        if ($user && Auth::validate($credentials)) {
            if ($user->role && in_array($user->role->name, ['admin', 'superadmin', 'user'])) {
                $this->sendOtp();
            } else {
                $this->addError('credentials', 'Anda tidak memiliki hak akses untuk masuk.');
            }
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
            $this->dispatch('otp-sent');
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

                // PERUBAHAN: Logika pengalihan disederhanakan untuk 'admin'/'superadmin' dan 'user'.
                $roleName = $user->role->name;
                if ($roleName === 'admin' || $roleName === 'superadmin') {
                    return $this->redirect(route('admin.dashboard'), navigate: true);
                } 
                elseif ($roleName === 'user') {
                    return $this->redirect(route('user.dashboard'), navigate: true);
                } 
                // Ini seharusnya tidak terjadi karena sudah dicek di attemptLogin, tapi sebagai pengaman.
                else {
                    Auth::logout();
                    return $this->redirect(route('login'));
                }
            } else {
                // Seharusnya tidak terjadi, tapi sebagai pengaman.
                $this->addError('credentials', 'User tidak ditemukan setelah verifikasi OTP.');
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

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }
}
