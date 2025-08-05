# Sistem Event + Pengecekan Berkala untuk Email Penilaian PKL

## **🎯 OVERVIEW**

Sistem ini mengimplementasikan **Event-Driven Architecture** dengan **pengecekan berkala setiap 10 detik** untuk mengirim email penilaian PKL secara otomatis.

## **🏗️ ARSITEKTUR SISTEM**

### **1. Event System**
- **Event**: `PrakerinSelesaiEvent`
- **Listener**: `SendPenilaianEmailListener`
- **Trigger**: Saat status prakerin berubah menjadi 'selesai'

### **2. Pengecekan Berkala**
- **Command**: `CheckPrakerinSelesaiCommand`
- **Schedule**: Setiap 10 detik
- **Fungsi**: Mencari prakerin selesai yang belum dikirim email

## **📁 FILE YANG DIBUAT/DIMODIFIKASI**

### **Event & Listener**
- `app/Events/PrakerinSelesaiEvent.php` - Event untuk prakerin selesai
- `app/Listeners/SendPenilaianEmailListener.php` - Listener untuk kirim email
- `app/Providers/EventServiceProvider.php` - Register event listener

### **Command & Schedule**
- `app/Console/Commands/CheckPrakerinSelesaiCommand.php` - Command pengecekan berkala
- `app/Console/Kernel.php` - Schedule setiap 10 detik

### **Observer (Dimodifikasi)**
- `app/Observers/PrakerinObserver.php` - Sekarang dispatch event alih-alih langsung kirim email

## **🔄 ALUR KERJA**

### **Trigger 1: Observer (Real-time)**
```
Status Prakerin Berubah → Observer → Dispatch Event → Listener → Kirim Email
```

### **Trigger 2: Pengecekan Berkala (Setiap 10 detik)**
```
Schedule → Command → Cek Database → Dispatch Event → Listener → Kirim Email
```

## **⚙️ KONFIGURASI**

### **1. Event Service Provider**
```php
protected $listen = [
    PrakerinSelesaiEvent::class => [
        SendPenilaianEmailListener::class,
    ],
];
```

### **2. Schedule (Kernel.php)**
```php
$schedule->command('prakerin:check-selesai')
    ->everyTenSeconds()
    ->withoutOverlapping()
    ->runInBackground();
```

## **🚀 CARA MENJALANKAN**

### **1. Test Command Manual**
```bash
php artisan prakerin:check-selesai
```

### **2. Test Event Manual**
```bash
php artisan tinker
event(new App\Events\PrakerinSelesaiEvent($prakerin));
```

### **3. Jalankan Schedule**
```bash
php artisan schedule:work
```

## **📊 MONITORING**

### **Log Events**
- `Event PrakerinSelesaiEvent triggered`
- `Email form penilaian berhasil dikirim via Event`
- `CheckPrakerinSelesaiCommand completed`

### **Metrics**
- Total prakerin selesai yang dicek
- Jumlah email berhasil dikirim
- Jumlah error yang terjadi

## **🔧 KEUNTUNGAN SISTEM BARU**

### **1. Event-Driven**
- ✅ **Decoupled**: Observer tidak langsung kirim email
- ✅ **Scalable**: Mudah tambah listener lain
- ✅ **Testable**: Event bisa di-test terpisah

### **2. Pengecekan Berkala**
- ✅ **Backup System**: Jika observer gagal, masih ada pengecekan berkala
- ✅ **Catch Missed**: Menangkap prakerin yang terlewat
- ✅ **Robust**: Sistem lebih tahan error

### **3. Monitoring**
- ✅ **Detailed Logging**: Setiap step tercatat
- ✅ **Error Handling**: Error tidak menghentikan sistem
- ✅ **Metrics**: Bisa track performance

## **🧪 TESTING SCENARIOS**

### **Scenario 1: Observer Trigger**
1. Ubah status prakerin dari 'aktif' ke 'selesai'
2. Observer ter-trigger
3. Event dispatched
4. Email dikirim

### **Scenario 2: Pengecekan Berkala**
1. Buat prakerin dengan status 'selesai' langsung di database
2. Jalankan command `prakerin:check-selesai`
3. Email dikirim untuk prakerin yang belum dinilai

### **Scenario 3: Schedule**
1. Jalankan `php artisan schedule:work`
2. Sistem otomatis cek setiap 10 detik
3. Email dikirim untuk prakerin baru

## **⚠️ TROUBLESHOOTING**

### **Error: Event tidak ter-trigger**
- Cek `EventServiceProvider` sudah register
- Cek observer sudah terdaftar di `AppServiceProvider`

### **Error: Command tidak berjalan**
- Cek schedule sudah benar di `Kernel.php`
- Jalankan `php artisan schedule:work`

### **Error: Email tidak terkirim**
- Cek log untuk detail error
- Cek konfigurasi email di `.env`
- Cek kompetensi sudah ada untuk jurusan

## **📈 PERFORMANCE**

### **Optimization**
- `withoutOverlapping()`: Mencegah command overlap
- `runInBackground()`: Command berjalan di background
- `sleep(1)`: Delay antar email untuk hindari spam

### **Monitoring**
- Log setiap event dan command
- Track jumlah email berhasil/error
- Monitor memory usage

## **🎉 KESIMPULAN**

Sistem baru ini memberikan:
- ✅ **Reliability**: Double trigger (observer + schedule)
- ✅ **Scalability**: Event-driven architecture
- ✅ **Monitoring**: Detailed logging dan metrics
- ✅ **Maintainability**: Code terpisah dan modular

**Sistem Event + Pengecekan Berkala berhasil diimplementasikan! 🚀** 