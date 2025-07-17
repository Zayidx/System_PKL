<?php

namespace App\Livewire\Auth;

use App\Mail\RegistrationOtpMail;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Register extends Component
{
    #[Layout("components.layouts.layout-auth")]
    #[Title("Halaman Registrasi")]

    // Properti untuk form registrasi, termasuk konfirmasi password
    public $email, $username, $password, $password_confirmation;

    // Properti untuk form OTP
    public $otp;
    public $showOtpForm = false;

    /**
     * Aturan validasi yang diperbarui.
     * 'password' divalidasi dengan 'confirmed' yang secara otomatis
     * akan memeriksa field 'password_confirmation'. Ini cara standar Laravel.
     */
    protected function rules()
    {
        return [
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|min:4|max:100',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' adalah kuncinya
        ];
    }

    /**
     * Pesan validasi kustom yang diperbaiki.
     * Kita menargetkan 'password.confirmed' untuk pesan error-nya.
     */
    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email ini sudah terdaftar.',
        'username.required' => 'Nama lengkap wajib diisi.',
        'username.min' => 'Nama lengkap minimal 4 karakter.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.', // Pesan untuk validasi 'confirmed'
        'otp.required' => 'Kode OTP wajib diisi.',
        'otp.digits' => 'Kode OTP harus 6 digit angka.',
    ];

    public function render()
    {
        return view('livewire.auth.register');
    }

    public function submitRegistrationDetails()
    {
        $this->validate(); // Validasi akan memeriksa semua rules, termasuk 'password.confirmed'
        $this->sendOtp();
    }

    public function sendOtp()
    {
        $otpCode = rand(100000, 999999);
        $redisKey = "otp_register:" . $this->email;
        Redis::setex($redisKey, 300, $otpCode);

        try {
            Mail::to($this->email)->send(new RegistrationOtpMail($otpCode));
            $this->showOtpForm = true;
            $this->resetErrorBag();
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

    public function verifyOtpAndCreateUser()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);
        $redisKey = "otp_register:" . $this->email;
        $storedOtp = Redis::get($redisKey);

        if ($storedOtp && $storedOtp == $this->otp) {
            $userRole = Role::where('name', 'user')->firstOrFail();
            
            $user = User::create([
                'roles_id' => $userRole->id,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            Redis::del($redisKey);
            Auth::login($user);
            request()->session()->regenerate();
            return $this->redirect(route('login'), navigate: true);
        }
        $this->addError('otp', 'Kode OTP tidak valid atau telah kedaluwarsa.');
    }

    public function cancelOtp()
    {
        // [PERBAIKAN] Menambahkan password_confirmation ke dalam reset
        $this->reset('otp', 'showOtpForm', 'email', 'username', 'password', 'password_confirmation');
        $this->resetErrorBag();
    }
}
