# Sistem Penilaian PKL

## 📋 **Deskripsi**
Sistem penilaian PKL yang otomatis mengirim form penilaian ke email perusahaan saat status prakerin berubah menjadi selesai. Sistem ini memungkinkan pembimbing perusahaan memberikan penilaian melalui link yang dikirim via email, dan nilai dapat dilihat oleh siswa dan staff hubin.

## 🎯 **Fitur Utama**

### **1. Email Otomatis**
- ✅ **Trigger:** Saat status prakerin berubah menjadi 'selesai'
- ✅ **Penerima:** Pembimbing perusahaan
- ✅ **Konten:** Form penilaian dengan token unik
- ✅ **Expired:** Token berlaku 7 hari

### **2. Form Penilaian Online**
- ✅ **Interface:** Form web yang user-friendly
- ✅ **Validasi:** Input nilai 0-100 dengan validasi
- ✅ **Kompetensi:** Dinamis berdasarkan jurusan siswa
- ✅ **Komentar:** Opsional untuk feedback tambahan

### **3. Dashboard Nilai**
- ✅ **Siswa:** Melihat nilai PKL mereka
- ✅ **Staff Hubin:** Monitoring nilai semua siswa
- ✅ **Detail:** Grafik dan statistik nilai
- ✅ **Export:** Fitur cetak nilai

## 🛠️ **Implementasi Teknis**

### **1. Observer Pattern**
**File:** `app/Observers/PrakerinObserver.php`

```php
class PrakerinObserver
{
    public function updated(Prakerin $prakerin): void
    {
        // Cek apakah status berubah menjadi 'selesai'
        if ($prakerin->isDirty('status_prakerin') && $prakerin->status_prakerin === 'selesai') {
            $this->sendPenilaianFormEmail($prakerin);
        }
    }
}
```

### **2. Email Template**
**File:** `app/Mail/PenilaianFormEmail.php`

```php
class PenilaianFormEmail extends Mailable
{
    public function __construct(Prakerin $prakerin, Siswa $siswa, Perusahaan $perusahaan, PembimbingPerusahaan $pembimbingPerusahaan, $kompetensi, $token)
    {
        // Kirim email dengan data lengkap
    }
}
```

### **3. Controller Penilaian**
**File:** `app/Http/Controllers/PenilaianController.php`

```php
class PenilaianController extends Controller
{
    public function showForm($token)
    {
        // Validasi token dan tampilkan form
    }
    
    public function submitPenilaian(Request $request, $token)
    {
        // Proses submit dan simpan nilai
    }
}
```

### **4. Livewire Components**

#### **NilaiSiswa Component**
**File:** `app/Livewire/User/NilaiSiswa.php`

```php
class NilaiSiswa extends Component
{
    public function render()
    {
        // Ambil prakerin selesai dengan nilai
        $prakerinSelesai = Prakerin::with(['perusahaan', 'pembimbingPerusahaan'])
            ->where('nis_siswa', $this->siswa->nis)
            ->where('status_prakerin', 'selesai')
            ->whereHas('pembimbingPerusahaan.penilaian')
            ->paginate($this->perPage);
            
        return view('livewire.user.nilai-siswa', [
            'prakerinSelesai' => $prakerinSelesai
        ]);
    }
}
```

#### **NilaiSiswaDashboard Component**
**File:** `app/Livewire/StaffHubin/NilaiSiswaDashboard.php`

```php
class NilaiSiswaDashboard extends Component
{
    public function render()
    {
        // Ambil siswa dengan statistik nilai
        $siswaList = Siswa::withCount(['prakerin as prakerin_selesai', 'prakerin as prakerin_dinilai'])
            ->where('id_kelas', $this->id_kelas)
            ->paginate($this->perPage);
            
        return view('livewire.staff-hubin.nilai-siswa-dashboard', [
            'siswaList' => $siswaList
        ]);
    }
}
```

## 📊 **Database Schema**

### **Tabel Penilaian**
```sql
CREATE TABLE penilaian (
    id_penilaian INT PRIMARY KEY AUTO_INCREMENT,
    nis_siswa VARCHAR(10),
    id_pemb_perusahaan INT,
    FOREIGN KEY (nis_siswa) REFERENCES siswa(nis),
    FOREIGN KEY (id_pemb_perusahaan) REFERENCES pembimbing_perusahaan(id_pembimbing)
);
```

### **Tabel Nilai (Pivot)**
```sql
CREATE TABLE nilai (
    id_penilaian INT,
    id_kompetensi TINYINT,
    nilai TINYINT,
    PRIMARY KEY (id_penilaian, id_kompetensi),
    FOREIGN KEY (id_penilaian) REFERENCES penilaian(id_penilaian),
    FOREIGN KEY (id_kompetensi) REFERENCES kompetensi(id_kompetensi)
);
```

## 🔄 **Alur Kerja Sistem**

### **1. Trigger Email**
```
Prakerin Status: 'aktif' → 'selesai'
↓
Observer PrakerinObserver::updated()
↓
sendPenilaianFormEmail()
↓
Generate Token & Send Email
```

### **2. Form Penilaian**
```
Email Received → Click Link
↓
Validate Token
↓
Show Form with Kompetensi
↓
Submit Nilai
↓
Save to Database
```

### **3. Dashboard Nilai**
```
Login → Dashboard
↓
View Nilai Component
↓
Display Prakerin with Nilai
↓
Detail Modal with Grafik
```

## 🎨 **UI/UX Features**

### **1. Email Template**
- ✅ **Responsive Design:** Tampilan yang baik di semua device
- ✅ **Professional Layout:** Header dengan gradient
- ✅ **Clear Information:** Data prakerin yang lengkap
- ✅ **Call-to-Action:** Tombol yang jelas untuk form

### **2. Form Penilaian**
- ✅ **Modern Interface:** Bootstrap 5 dengan custom styling
- ✅ **Input Validation:** Real-time validation
- ✅ **Progress Indicator:** Loading states
- ✅ **Error Handling:** User-friendly error messages

### **3. Dashboard Nilai**
- ✅ **Card Layout:** Informasi yang terorganisir
- ✅ **Data Tables:** Sorting dan filtering
- ✅ **Modal Details:** Popup dengan detail lengkap
- ✅ **Charts:** Visualisasi data nilai

## 📈 **Fitur Dashboard**

### **Untuk Siswa:**
- ✅ **Daftar Prakerin:** Semua prakerin selesai dengan nilai
- ✅ **Status Nilai:** Sudah dinilai atau belum
- ✅ **Rata-rata Nilai:** Perhitungan otomatis
- ✅ **Detail Modal:** Grafik dan breakdown nilai
- ✅ **Export:** Fitur cetak nilai

### **Untuk Staff Hubin:**
- ✅ **Monitoring Kelas:** Nilai semua siswa per kelas
- ✅ **Statistik:** Rata-rata, tertinggi, terendah
- ✅ **Filter & Search:** Pencarian berdasarkan nama/NIS
- ✅ **Detail Siswa:** Modal dengan semua prakerin siswa
- ✅ **Export:** Cetak laporan nilai

## 🔧 **Error Handling**

### **1. Token Validation**
- ✅ **Invalid Token:** Halaman error yang informatif
- ✅ **Expired Token:** Pesan dengan solusi
- ✅ **Already Rated:** Tampilkan nilai yang sudah ada
- ✅ **Data Not Found:** Error handling untuk data hilang

### **2. Form Validation**
- ✅ **Input Range:** Nilai 0-100
- ✅ **Required Fields:** Semua kompetensi harus dinilai
- ✅ **Database Errors:** Try-catch dengan logging
- ✅ **User Feedback:** Alert messages yang jelas

### **3. Blade Template Errors**
- ✅ **Undefined Variable:** Menggunakan `use ($variable)` dalam closure
- ✅ **Scope Issues:** Memastikan variabel tersedia dalam scope
- ✅ **Null Safety:** Pengecekan null sebelum mengakses properti
- ✅ **Error Logging:** Log error untuk debugging

#### **Contoh Perbaikan Error:**
```php
// SEBELUM (Error):
$prakerinDinilai = $prakerinSelesai->filter(function($prakerin) {
    return $prakerin->pembimbingPerusahaan->penilaian()
        ->where('nis_siswa', $siswa->nis) // Error: $siswa tidak tersedia
        ->exists();
});

// SESUDAH (Fixed):
$prakerinDinilai = $prakerinSelesai->filter(function($prakerin) use ($siswa) {
    return $prakerin->pembimbingPerusahaan->penilaian()
        ->where('nis_siswa', $siswa->nis) // ✅ $siswa tersedia
        ->exists();
});
```

## 📧 **Email System**

### **1. Template Features**
```html
<!-- Header dengan gradient -->
<div class="header">
    <h1>📋 Form Penilaian PKL</h1>
    <p>Sistem Informasi Prakerin</p>
</div>

<!-- Informasi Prakerin -->
<div class="info-box">
    <h3>📋 Informasi Prakerin</h3>
    <!-- Data lengkap prakerin -->
</div>

<!-- Call-to-Action -->
<a href="{{ url('/penilaian/form/' . $token) }}" class="btn">
    📝 Isi Form Penilaian
</a>
```

### **2. Token Management**
```php
// Generate token
$token = \Str::random(64);

// Store in cache
\Cache::put("penilaian_token_{$token}", [
    'prakerin_id' => $prakerin->id_prakerin,
    'nis_siswa' => $siswa->nis,
    'pembimbing_id' => $pembimbingPerusahaan->id_pembimbing,
    'expires_at' => now()->addDays(7)
], now()->addDays(7));
```

## 🚀 **Routes Configuration**

### **1. User Routes**
```php
Route::prefix('user')->name('user.')->middleware('role:user')->group(function () {
    Route::get('/nilai', NilaiSiswa::class)->name('nilai');
});
```

### **2. Staff Hubin Routes**
```php
Route::prefix('staffhubin')->name('staffhubin.')->middleware('role:staffhubin')->group(function () {
    Route::get('/nilai/kelas/{id_kelas}', NilaiSiswaDashboard::class)->name('nilai.siswa');
});
```

### **3. Public Routes**
```php
// Routes untuk form penilaian (tidak memerlukan auth)
Route::get('/penilaian/form/{token}', [PenilaianController::class, 'showForm'])->name('penilaian.form');
Route::post('/penilaian/submit/{token}', [PenilaianController::class, 'submitPenilaian'])->name('penilaian.submit');
```

## 📊 **Monitoring & Analytics**

### **1. Logging System**
```php
// Log email sent
\Log::info('Email form penilaian berhasil dikirim', [
    'prakerin_id' => $prakerin->id_prakerin,
    'siswa_nis' => $siswa->nis,
    'pembimbing_email' => $pembimbingPerusahaan->email,
    'token' => $token
]);

// Log penilaian submitted
\Log::info('Penilaian berhasil disimpan', [
    'penilaian_id' => $penilaian->id_penilaian,
    'nis_siswa' => $tokenData['nis_siswa'],
    'pembimbing_id' => $tokenData['pembimbing_id']
]);
```

### **2. Statistics Dashboard**
- ✅ **Total Penilaian:** Jumlah penilaian per periode
- ✅ **Response Rate:** Persentase pembimbing yang merespon
- ✅ **Average Score:** Rata-rata nilai per jurusan/kelas
- ✅ **Trend Analysis:** Grafik perkembangan nilai

## 🎯 **Integration Points**

### **1. Tombol "Cek Nilai"**
- ✅ **Riwayat Prakerin:** Tombol di prakerin selesai
- ✅ **Staff Hubin Dashboard:** Tombol di status siswa
- ✅ **Direct Link:** Navigasi langsung ke halaman nilai

### **2. Real-time Updates**
- ✅ **Auto-refresh:** Dashboard ter-update otomatis
- ✅ **Notification:** Alert saat ada nilai baru
- ✅ **Status Sync:** Status penilaian real-time

## 🔒 **Security Features**

### **1. Token Security**
- ✅ **Random Generation:** 64 karakter random
- ✅ **Time-based Expiry:** 7 hari validity
- ✅ **Single Use:** Token dihapus setelah submit
- ✅ **Cache Storage:** Temporary storage yang aman

### **2. Data Validation**
- ✅ **Input Sanitization:** Clean input data
- ✅ **Range Validation:** Nilai 0-100
- ✅ **SQL Injection Protection:** Eloquent ORM
- ✅ **XSS Protection:** Blade template escaping

## 📱 **Responsive Design**

### **1. Mobile Optimization**
- ✅ **Touch-friendly:** Button size yang sesuai
- ✅ **Readable Text:** Font size yang optimal
- ✅ **Swipe Gestures:** Mobile navigation
- ✅ **Offline Capability:** Cache untuk performance

### **2. Cross-browser Compatibility**
- ✅ **Chrome/Safari:** Full support
- ✅ **Firefox/Edge:** Compatible
- ✅ **Mobile Browsers:** Optimized
- ✅ **Progressive Enhancement:** Graceful degradation

## 🚀 **Performance Optimization**

### **1. Database Queries**
- ✅ **Eager Loading:** Reduce N+1 queries
- ✅ **Indexing:** Optimized database indexes
- ✅ **Caching:** Cache frequently accessed data
- ✅ **Pagination:** Large dataset handling

### **2. Frontend Performance**
- ✅ **Lazy Loading:** Load data on demand
- ✅ **Image Optimization:** Compressed images
- ✅ **CSS/JS Minification:** Reduced file sizes
- ✅ **CDN Usage:** Fast content delivery

## 📋 **Testing Scenarios**

### **1. Email Flow Testing**
```
1. Ubah status prakerin menjadi 'selesai'
2. Cek email terkirim ke pembimbing
3. Klik link di email
4. Isi form penilaian
5. Submit dan cek nilai tersimpan
```

### **2. Dashboard Testing**
```
1. Login sebagai siswa
2. Akses halaman nilai
3. Cek data prakerin dengan nilai
4. Klik detail untuk melihat grafik
5. Test fitur cetak
```

### **3. Error Handling Testing**
```
1. Test token invalid
2. Test token expired
3. Test duplicate submission
4. Test database errors
5. Test network issues
```

## 🎉 **Benefits**

### **1. Automation**
- ✅ **No Manual Work:** Email otomatis terkirim
- ✅ **Time Saving:** Tidak perlu kirim manual
- ✅ **Consistency:** Format email yang seragam
- ✅ **Tracking:** Log semua aktivitas

### **2. User Experience**
- ✅ **Easy Access:** Link langsung ke form
- ✅ **Mobile Friendly:** Responsive design
- ✅ **Clear Interface:** UI yang intuitif
- ✅ **Fast Response:** Real-time feedback

### **3. Data Management**
- ✅ **Centralized:** Semua data di satu tempat
- ✅ **Searchable:** Pencarian yang mudah
- ✅ **Exportable:** Fitur cetak/laporan
- ✅ **Analytics:** Statistik dan grafik

---

**Sistem penilaian PKL ini memberikan solusi lengkap untuk otomatisasi proses penilaian, dari pengiriman email hingga dashboard monitoring yang komprehensif! 🎉**

**Dengan integrasi yang seamless dan user experience yang superior, sistem ini memastikan bahwa setiap prakerin yang selesai akan otomatis mendapatkan penilaian yang tepat waktu dan akurat.** 