<?php

namespace App\Livewire\Auth;

use App\Mail\ForgotPasswordOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Layout('components.layouts.layout-auth')]
    #[Title('Lupa Password')]

    public $email;
    public $otp;
    public $showOtpForm = false;
    public $showResetForm = false;
    public $password;
    public $password_confirmation;

    protected function rules()
    {
        if ($this->showResetForm) {
            return [
                'password' => 'required|string|min:6|confirmed',
            ];
        }
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.exists' => 'Email tidak ditemukan.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'otp.required' => 'Kode OTP wajib diisi.',
        'otp.digits' => 'Kode OTP harus 6 digit angka.',
    ];

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }

    public function submitEmail()
    {
        $this->validate();
        $this->sendOtp();
    }

    public function sendOtp()
    {
        $otpCode = rand(100000, 999999);
        $redisKey = "otp_forgot:" . $this->email;
        Redis::setex($redisKey, 300, $otpCode);

        try {
            Mail::to($this->email)->send(new ForgotPasswordOtpMail($otpCode));
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

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);
        $redisKey = "otp_forgot:" . $this->email;
        $storedOtp = Redis::get($redisKey);

        if ($storedOtp && $storedOtp == $this->otp) {
            $this->showResetForm = true;
            Redis::del($redisKey);
            $this->reset('otp');
            $this->resetErrorBag();
        } else {
            $this->addError('otp', 'Kode OTP tidak valid atau telah kedaluwarsa.');
        }
    }

    public function resetPassword()
    {
        $this->validate();
        $user = User::where('email', $this->email)->first();
        if ($user) {
            $user->password = Hash::make($this->password);
            $user->save();
            $this->reset();
            session()->flash('success', 'Password berhasil diubah. Silakan login.');
            // Redirect otomatis ke login
            return $this->redirect(route('login'), navigate: true);
        } else {
            $this->addError('credentials', 'User tidak ditemukan.');
        }
    }

    public function cancelOtp()
    {
        $this->reset('otp', 'showOtpForm', 'showResetForm', 'email', 'password', 'password_confirmation');
        $this->resetErrorBag();
    }
}
