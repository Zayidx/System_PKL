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
use Livewire\Component;

class Register extends Component
{
    #[Layout("components.layouts.layout-auth")]
    // Properti untuk form registrasi
    public $email, $username, $password;

    // Properti untuk form OTP
    public $otp;
    public $showOtpForm = false;

    /**
     * Aturan validasi yang diperbarui.
     * Email harus unik di tabel 'users'.
     */
    protected function rules()
    {
        return [
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|min:4|max:100',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Pesan validasi kustom yang diperbaiki.
     */
    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email ini sudah terdaftar.',
        'username.required' => 'Nama lengkap wajib diisi.',
        'username.min' => 'Nama lengkap minimal 4 karakter.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'otp.required' => 'Kode OTP wajib diisi.',
        'otp.digits' => 'Kode OTP harus 6 digit angka.',
    ];

    /**
     * Method ini dipanggil saat form registrasi awal di-submit.
     * Fungsinya bukan untuk mendaftar, tapi untuk memvalidasi dan mengirim OTP.
     */
    public function submitRegistrationDetails()
    {
        $this->validate(); // Validasi email, username, password

        try {
            // Buat dan kirim OTP
            $otpCode = rand(100000, 999999);
            $redisKey = "otp_register:" . $this->email;

            // Simpan OTP di Redis dengan masa berlaku 5 menit (300 detik)
            Redis::setex($redisKey, 300, $otpCode);

            // Kirim OTP ke email pengguna menggunakan Mailable yang baru dibuat
            Mail::to($this->email)->send(new RegistrationOtpMail($otpCode));

            // Tampilkan form OTP setelah email berhasil dikirim
            $this->showOtpForm = true;
            $this->resetErrorBag(); // Hapus error lama jika ada

        } catch (\Exception $e) {
            // Jika gagal kirim email, tampilkan error
            $this->addError('email', 'Gagal mengirim OTP. Pastikan konfigurasi email benar dan coba lagi.');
            report($e); // Laporkan error ke log untuk debugging
        }
    }

    /**
     * Method ini dipanggil saat form OTP di-submit.
     * Fungsinya untuk verifikasi OTP, membuat user, dan login.
     */
    public function verifyOtpAndCreateUser()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);

        $redisKey = "otp_register:" . $this->email;
        $storedOtp = Redis::get($redisKey);

        if ($storedOtp && $storedOtp == $this->otp) {
            // OTP cocok, lanjutkan membuat user

            // Cari role 'user' secara dinamis untuk mendapatkan ID-nya
            $userRole = Role::where('name', 'user')->first();

            if (!$userRole) {
                // Jika role 'user' tidak ditemukan, gagalkan proses
                $this->addError('otp', 'Konfigurasi role sistem tidak ditemukan. Hubungi admin.');
                return;
            }

            $user = User::create([
                'roles_id' => $userRole->id, // Gunakan ID dari role 'user'
                'name' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Hapus OTP dari Redis setelah berhasil digunakan
            Redis::del($redisKey);

            // Login-kan user yang baru dibuat
            Auth::login($user);
            request()->session()->regenerate();

            // Arahkan ke dashboard yang sesuai untuk user biasa
            // Ganti 'user.dashboard' dengan nama route Anda yang sebenarnya
            return $this->redirect(route('login'), navigate: true);

        }

        // Jika OTP salah, tidak ada, atau sudah kedaluwarsa
        $this->addError('otp', 'Kode OTP tidak valid atau telah kedaluwarsa.');
    }

    /**
     * Membatalkan proses OTP dan kembali ke form registrasi awal.
     */
    public function cancelOtp()
    {
        $this->reset('otp', 'showOtpForm');
        $this->resetErrorBag(); // Hapus semua pesan error
    }

    /**
     * Mengembalikan tampilan registrasi.
     */
    public function render()
    {
        return view('livewire.auth.register');
    }
}
