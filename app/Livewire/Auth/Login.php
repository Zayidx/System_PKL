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
    // Properti untuk form login awal
    #[Validate('required|email')]
    public $email;

    #[Validate('required')]
    public $password;

    // Properti baru untuk menampung input OTP dari pengguna
    // Validasi dipindahkan ke method verifyOtpAndLogin agar tidak terpanggil saat login awal
    public $otp;

    // Properti untuk mengontrol tampilan form (login atau OTP)
    public $showOtpForm = false;

    /**
     * Menampilkan view komponen.
     * Menggunakan Layout dan Title attributes untuk halaman.
     */
    #[Title('Halaman Login')]
    #[Layout('components.layouts.layout-auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }

    /**
     * Metode ini memvalidasi kredensial awal dan mengirim OTP jika valid.
     * Tidak langsung melakukan login.
     */
    public function attemptLogin()
    {
        // Validasi hanya email dan password saat percobaan login
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        // Auth::validate() hanya memeriksa kredensial tanpa membuat session/cookie.
        // Ini adalah cara yang aman untuk memeriksa apakah pengguna ada sebelum mengirim OTP.
        if (Auth::validate($credentials)) {
            // Kredensial benar, lanjutkan untuk kirim OTP
            $this->sendOtp();
        } else {
            // Jika otentikasi gagal, tambahkan pesan error
            $this->addError('credentials', 'Gagal masuk, email atau password salah!');
        }
    }

    /**
     * Fungsi untuk membuat, menyimpan, dan mengirim OTP.
     */
    public function sendOtp()
    {
        $otpCode = rand(100000, 999999);
        $redisKey = "otp:" . $this->email;

        // Simpan OTP di Redis dengan masa berlaku 5 menit (300 detik)
        Redis::setex($redisKey, 300, $otpCode);

        // Kirim OTP ke email pengguna
        try {
            Mail::to($this->email)->send(new LoginOtpMail($otpCode));
            // Tampilkan form OTP setelah email berhasil dikirim
            $this->showOtpForm = true;
            $this->reset('password'); // Kosongkan password untuk keamanan
        } catch (\Exception $e) {
            // Jika gagal kirim email, tampilkan error
            $this->addError('credentials', 'Gagal mengirim OTP. Pastikan konfigurasi email benar.');
            // Laporkan error ke log untuk debugging
            report($e);
        }
    }

    /**
     * Metode baru untuk memverifikasi OTP dan menyelesaikan proses login.
     */
    public function verifyOtpAndLogin()
    {
        // Validasi hanya field OTP saat verifikasi
        $this->validate([
            'otp' => 'required|numeric|digits:6'
        ]);

        $redisKey = "otp:" . $this->email;
        $storedOtp = Redis::get($redisKey);

        // Periksa apakah OTP yang disimpan ada dan cocok dengan input pengguna
        if ($storedOtp && $storedOtp == $this->otp) {
            // OTP benar, cari user berdasarkan email
            $user = User::where('email', $this->email)->first();

            if ($user) {
                Auth::login($user); // Loginkan pengguna
                Redis::del($redisKey); // Hapus OTP dari Redis setelah berhasil digunakan

                request()->session()->regenerate(); // Regenerasi session untuk keamanan

                // Arahkan pengguna berdasarkan rolenya
                if ($user->role && $user->role->name === 'superadmin') {
                    return $this->redirect(route('admin.dashboard'), navigate: true);
                } else {
                    // Ganti dengan route default jika role bukan superadmin
                    return $this->redirect(route('admin.master-admin.user'), navigate: true);
                }
            }
        }

        // Jika OTP salah, tidak ada, atau sudah kedaluwarsa
        $this->addError('otp', 'Kode OTP tidak valid atau telah kedaluwarsa.');
    }

    /**
     * Membatalkan proses OTP dan kembali ke form login awal.
     */
    public function cancelOtp()
    {
        $this->reset('otp', 'showOtpForm');
        $this->resetErrorBag(); // Hapus semua pesan error
    }

    /**
     * Fungsi untuk logout pengguna.
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }
}
