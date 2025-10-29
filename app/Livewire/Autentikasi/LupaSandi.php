<?php

namespace App\Livewire\Autentikasi;

use App\Mail\ForgotPasswordOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Proses pemulihan kata sandi berbasis OTP:
 * - Mengirim OTP ke email pengguna untuk verifikasi kepemilikan akun.
 * - Mengizinkan pengaturan ulang kata sandi setelah OTP tervalidasi.
 * - Menangani kasus kegagalan pengiriman dan reset state ketika dibatalkan.
 */
class LupaSandi extends Component
{
    #[Layout('components.layouts.layout-auth')]
    #[Title('Lupa Kata Sandi')]

    public $email;
    public $otp;
    public $showOtpForm = false;
    public $showResetForm = false;
    public $password;
    public $password_confirmation;

    /**
     * Validasi dinamis untuk tahap pengiriman email maupun reset password.
     */
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

    /**
     * Menampilkan tampilan langkah-langkah lupa kata sandi.
     */
    public function render()
    {
        return view('livewire.autentikasi.lupa-sandi');
    }

    /**
     * Memvalidasi email lalu mengirim OTP awal.
     */
    public function submitEmail()
    {
        $this->validate();
        $this->sendOtp();
    }

    /**
     * Membuat OTP 6 digit dan mengirimkannya ke email; disimpan sementara di Redis.
     */
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

    /**
     * Mengirim ulang OTP menggunakan mekanisme yang sama.
     */
    public function resendOtp()
    {
        $this->sendOtp();
    }

    /**
     * Memastikan OTP valid; bila benar, menampilkan formulir reset password.
     */
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

    /**
     * Mengganti kata sandi user setelah validasi berhasil, lalu mengarahkan ke halaman masuk.
     */
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
            return $this->redirect(route('masuk'), navigate: true);
        } else {
            $this->addError('credentials', 'User tidak ditemukan.');
        }
    }

    /**
     * Membatalkan alur OTP dan mengembalikan form ke kondisi awal.
     */
    public function cancelOtp()
    {
        $this->reset('otp', 'showOtpForm', 'showResetForm', 'email', 'password', 'password_confirmation');
        $this->resetErrorBag();
    }
}
