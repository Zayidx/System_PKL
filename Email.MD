# Panduan Livewire: Form Kirim Email Sederhana

Dokumen ini memandu Anda membuat proyek Laravel 12 baru dengan satu komponen Livewire yang hanya berisi form pengiriman email (`subject`, `message`, dan `recipient`). Setiap tahap menampilkan potongan kode yang relevan; versi lengkap tiap berkas tersedia pada bagian **Lampiran** di akhir.

## 1. Persiapan Awal
- PHP 8.2, Composer 2.x, dan Node.js 18+ sudah terinstal.
- SMTP lokal seperti Mailpit (`mailpit` pada port 1025/8025) atau kredensial layanan email lain.
- Terminal dan editor teks.

## 2. Membuat Proyek Laravel & Menjalankan Server
```bash
composer create-project laravel/laravel LivewireMail
cd LivewireMail
php artisan serve
```
Akses `http://127.0.0.1:8000` untuk memastikan instalasi berhasil. Biarkan server artisan berjalan selama pengembangan.

## 3. Mengonfigurasi Mailer
Salin `.env.example` menjadi `.env`, lalu isi variabel mail (contoh Mailpit):
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=no-reply@example.test
MAIL_FROM_NAME="Livewire Mailer"
```
Selesaikan dengan `php artisan config:clear` agar nilai baru dikonsumsi aplikasi.

## 4. Instalasi Livewire 3
```bash
composer require livewire/livewire
php artisan livewire:install
npm install
npm run dev
```
Perintah `livewire:install` menambahkan helper pada `resources/views/welcome.blade.php` dan file Vite. Anda akan mengganti halaman welcome dengan tampilan khusus pada langkah berikutnya.

## 5. Membuat Komponen Livewire
```bash
php artisan make:livewire SendEmailForm
```
Perintah ini membuat `app/Livewire/SendEmailForm.php` dan view `resources/views/livewire/send-email-form.blade.php`.

### 5.1 Menulis Logika Komponen
Edit kelas komponen agar memvalidasi input dan mengirim email menggunakan `Mail::raw`:
```php
class SendEmailForm extends Component
{
    public string $subject = '';
    public string $recipient = '';
    public string $message = '';
    public bool $sent = false;

    protected $rules = [
        'subject' => 'required|string|max:120',
        'recipient' => 'required|email',
        'message' => 'required|string|min:5',
    ];

    public function sendEmail(): void
    {
        $validated = $this->validate();

        Mail::raw($validated['message'], function ($mail) use ($validated) {
            $mail->to($validated['recipient'])
                 ->subject($validated['subject']);
        });

        $this->reset(['subject', 'recipient', 'message']);
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.send-email-form');
    }
}
```

### 5.2 Menyusun View Komponen
Pada `resources/views/livewire/send-email-form.blade.php`, buat form tiga bidang dengan validasi Livewire:
```blade
<div class="max-w-xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-semibold mb-4">Livewire Email Sender</h1>

    @if ($sent)
        <p class="mb-4 rounded bg-green-100 p-3 text-green-800">Email berhasil dikirim.</p>
    @endif

    <form wire:submit.prevent="sendEmail" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Subject</label>
            <input type="text" wire:model.defer="subject" class="mt-1 w-full rounded border p-2">
            @error('subject') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Kirim ke</label>
            <input type="email" wire:model.defer="recipient" class="mt-1 w-full rounded border p-2">
            @error('recipient') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Pesan</label>
            <textarea rows="5" wire:model.defer="message" class="mt-1 w-full rounded border p-2"></textarea>
            @error('message') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="w-full rounded bg-indigo-600 py-2 text-white hover:bg-indigo-700">
            Kirim Email
        </button>
    </form>
</div>
```

## 6. Mengganti Tampilan Utama
Buat file `resources/views/send-email.blade.php` untuk menjadi halaman utama:
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Livewire Mailer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <livewire:send-email-form />
    </div>

    @livewireScripts
</body>
</html>
```

Kemudian daftarkan rute sederhana di `routes/web.php`:
```php
use Illuminate\Support\Facades\Route;

Route::view('/', 'send-email')->name('send-email');
```

## 7. Menjalankan Aplikasi
Jalankan server Laravel (`php artisan serve`) dan Vite (`npm run dev`) jika belum. Buka `http://127.0.0.1:8000`, isi form dengan alamat email uji, lalu kirim. Untuk Mailpit, buka `http://127.0.0.1:8025` dan pastikan pesan masuk.

## 8. Pengujian Otomatis (Opsional)
Tambahkan uji fitur sederhana untuk memverifikasi Livewire:
```bash
php artisan make:test SendEmailFormTest
```

Isi `tests/Feature/SendEmailFormTest.php`:
```php
use App\Livewire\SendEmailForm;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('form mengirim email', function () {
    Mail::fake();

    Livewire::test(SendEmailForm::class)
        ->set('subject', 'Test Subject')
        ->set('recipient', 'user@example.com')
        ->set('message', 'Pesan pengujian Livewire')
        ->call('sendEmail')
        ->assertSet('sent', true);

    Mail::assertSent(function ($mail) {
        return $mail->hasTo('user@example.com')
            && $mail->subject === 'Test Subject';
    });
});
```

Jalankan dengan `php artisan test --filter=SendEmailFormTest`.

## 9. Troubleshooting
- **Pesan tidak muncul di Mailpit:** pastikan `MAIL_HOST`, `MAIL_PORT`, dan `MAIL_ENCRYPTION` sesuai. Restart Mailpit jika perlu.
- **Livewire binding tidak jalan:** jalankan kembali `npm run dev` dan segarkan halaman; periksa console browser untuk error JS.
- **Validasi selalu gagal:** cek format email/subject pada form dan pastikan properti Livewire sudah bertipe string.

## Lampiran: Kode Lengkap
Gunakan daftar berikut untuk menyalin seluruh isi berkas setelah langkah konfigurasi selesai.

### `routes/web.php`
```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'send-email')->name('send-email');
```

### `resources/views/send-email.blade.php`
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Livewire Mailer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-100 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <livewire:send-email-form />
    </div>

    @livewireScripts
</body>
</html>
```

### `app/Livewire/SendEmailForm.php`
```php
<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class SendEmailForm extends Component
{
    public string $subject = '';
    public string $recipient = '';
    public string $message = '';
    public bool $sent = false;

    protected $rules = [
        'subject' => 'required|string|max:120',
        'recipient' => 'required|email',
        'message' => 'required|string|min:5',
    ];

    public function sendEmail(): void
    {
        $validated = $this->validate();

        Mail::raw($validated['message'], function ($mail) use ($validated) {
            $mail->to($validated['recipient'])
                 ->subject($validated['subject']);
        });

        $this->reset(['subject', 'recipient', 'message']);
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.send-email-form');
    }
}
```

### `resources/views/livewire/send-email-form.blade.php`
```blade
<div class="max-w-xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-semibold mb-4">Livewire Email Sender</h1>

    @if ($sent)
        <p class="mb-4 rounded bg-green-100 p-3 text-green-800">Email berhasil dikirim.</p>
    @endif

    <form wire:submit.prevent="sendEmail" class="space-y-4">
        <div>
            <label class="block text-sm font-medium">Subject</label>
            <input type="text" wire:model.defer="subject" class="mt-1 w-full rounded border p-2">
            @error('subject') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Kirim ke</label>
            <input type="email" wire:model.defer="recipient" class="mt-1 w-full rounded border p-2">
            @error('recipient') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Pesan</label>
            <textarea rows="5" wire:model.defer="message" class="mt-1 w-full rounded border p-2"></textarea>
            @error('message') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="w-full rounded bg-indigo-600 py-2 text-white hover:bg-indigo-700">
            Kirim Email
        </button>
    </form>
</div>
```

Dengan panduan ini, sebuah proyek Laravel 12 minimalis dengan satu komponen Livewire siap mengirim email sesuai input pengguna. Sesuaikan tampilan, validasi, atau metode pengiriman sesuai kebutuhan lanjutan.
