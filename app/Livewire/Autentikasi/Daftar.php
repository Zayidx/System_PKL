<?php

namespace App\Livewire\Autentikasi;

use App\Mail\RegistrationOtpMail;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas; // Pastikan model Kelas di-import
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout("components.layouts.layout-auth")]
#[Title("Halaman Registrasi Siswa")]
/**
 * Komponen pendaftaran siswa:
 * - Mengumpulkan data awal calon siswa dan mengirim OTP ke email.
 * - Memverifikasi OTP sebelum membuat akun user & entitas siswa di database.
 * - Mengunggah foto profil dan langsung login setelah registrasi sukses.
 */
class Daftar extends Component
{
    use WithFileUploads;

    public $nis, $email, $username, $kontak_siswa, $password, $password_confirmation, $foto;
    public $tempat_lahir, $tanggal_lahir;
    public $otp;
    public $showOtpForm = false;

    /**
     * Validasi untuk setiap langkah input registrasi.
     */
    protected function rules()
    {
        return [
            'nis' => 'required|numeric|digits_between:8,12|unique:siswa,nis',
            'email' => 'required|email|max:100|unique:users,email',
            'username' => 'required|string|min:4|max:100',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'kontak_siswa' => 'required|numeric|digits_between:10,15',
            'password' => 'required|string|min:6|confirmed',
            'foto' => 'required|image|max:2048',
        ];
    }

    protected $messages = [
        'nis.required' => 'NIS wajib diisi.',
        'nis.unique' => 'NIS ini sudah terdaftar.',
        'email.unique' => 'Email ini sudah terdaftar.',
        'username.required' => 'Nama lengkap wajib diisi.',
        'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
        'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
        'kontak_siswa.required' => 'Nomor telepon wajib diisi.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'foto.required' => 'Foto profil wajib diunggah.',
    ];

    /**
     * Menyajikan tampilan formulir registrasi dan OTP.
     */
    public function render()
    {
        return view('livewire.autentikasi.daftar');
    }

    /**
     * Melakukan validasi awal lalu mengirim OTP ke email.
     */
    public function submitRegistrationDetails()
    {
        $this->validate();
        $this->sendOtp();
    }

    /**
     * Menghasilkan OTP 6 digit, menyimpannya di Redis, dan mengirim email ke calon siswa.
     * Jika gagal, error ditampilkan dan dicatat.
     */
    public function sendOtp()
    {
        $otpCode = rand(100000, 999999);
        $redisKey = "otp_register:" . $this->email;
        Redis::setex($redisKey, 300, $otpCode); // OTP berlaku 5 menit

        try {
            Mail::to($this->email)->send(new RegistrationOtpMail($otpCode));
            $this->showOtpForm = true;
            $this->resetErrorBag();
            $this->dispatch('otp-sent');
        } catch (\Exception $e) {
            $this->addError('credentials', 'Gagal mengirim OTP. Pastikan konfigurasi email benar dan coba lagi.');
            report($e); // Melaporkan error ke log untuk debugging
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
     * Memvalidasi OTP dan membuat akun user+siswa dalam satu transaksi.
     * Setelah sukses, OTP dibersihkan dan pengguna langsung login.
     */
    public function verifyOtpAndCreateUser()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);
        $redisKey = "otp_register:" . $this->email;
        $storedOtp = Redis::get($redisKey);

        if ($storedOtp && $storedOtp == $this->otp) {
            try {
                // Menggunakan DB::transaction untuk memastikan semua query berhasil atau tidak sama sekali.
                $user = DB::transaction(function () use ($redisKey) {
                    // 1. Cari Role 'user'. Gagal jika tidak ditemukan.
                    $userRole = Role::where('name', 'user')->firstOrFail();

                    // 2. Cari Kelas default 'N/A'. Ini asumsi, bisa disesuaikan.
                    // Sebaiknya ada cara yang lebih baik untuk menentukan kelas awal.
                    $defaultKelas = Kelas::where('nama_kelas', 'N/A')->first();
                    if (!$defaultKelas) {
                        // Jika kelas N/A tidak ada, berikan pesan error yang jelas.
                        throw new \Exception('Konfigurasi kelas default (N/A) tidak ditemukan. Hubungi admin.');
                    }

                    // 3. Simpan foto ke storage dan dapatkan path-nya.
                    $fotoPath = $this->foto->store('fotos/profil', 'public');

                    // 4. Buat data di tabel 'users' terlebih dahulu.
                    $newUser = User::create([
                        'roles_id' => $userRole->id, // Menggunakan role 'user'
                        'username' => $this->username,
                        'email' => $this->email,
                        'password' => Hash::make($this->password),
                        'foto' => $fotoPath,
                    ]);

                    // 5. Buat data di tabel 'siswa' menggunakan ID dari user yang baru dibuat.
                    Siswa::create([
                        'nis' => $this->nis,
                        'user_id' => $newUser->id, // Ini adalah kuncinya!
                        'id_kelas' => $defaultKelas->id_kelas,
                        'id_jurusan' => $defaultKelas->id_jurusan,
                        'nama_siswa' => $this->username,
                        'tempat_lahir' => $this->tempat_lahir,
                        'tanggal_lahir' => $this->tanggal_lahir,
                        'kontak_siswa' => $this->kontak_siswa,
                    ]);

                    // 6. Hapus OTP dari Redis setelah berhasil digunakan.
                    Redis::del($redisKey);
                    
                    // 7. Kembalikan user yang baru dibuat untuk proses login.
                    return $newUser;
                });

                // Setelah transaksi berhasil, loginkan user.
                Auth::login($user);
                request()->session()->regenerate();

                // Redirect ke dashboard user setelah login berhasil.
                return $this->redirect(route('pengguna.dasbor'), navigate: true);

            } catch (\Exception $e) {
                // Jika terjadi error di dalam transaksi, tampilkan pesan.
                $this->addError('credentials', 'Gagal membuat akun. Terjadi kesalahan pada server: ' . $e->getMessage());
                report($e); // Laporkan error untuk dianalisis
                return;
            }
        }
        
        $this->addError('otp', 'Kode OTP tidak valid atau telah kedaluwarsa.');
    }

    /**
     * Membatalkan proses OTP dan mereset seluruh input sehingga formulir kembali ke kondisi awal.
     */
    public function cancelOtp()
    {
        $this->reset(['nis', 'email', 'username', 'kontak_siswa', 'password', 'password_confirmation', 'foto', 'tempat_lahir', 'tanggal_lahir', 'otp', 'showOtpForm']);
        $this->resetErrorBag();
    }
}
