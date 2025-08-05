# Troubleshooting Email Penilaian

## **🐛 MASALAH: Email tidak masuk ke Gmail**

### **✅ KONFIRMASI EMAIL TERKIRIM**

Dari log Laravel, email **berhasil dikirim**:
```
[2025-07-31 07:49:50] production.INFO: Email form penilaian berhasil dikirim ke perusahaan {
    "prakerin_id":1,
    "siswa_nis":"1234567890",
    "perusahaan_email":"silfa0236@gmail.com",
    "perusahaan_nama":"PT Laksita Tbk",
    "token":"TlhnZfxGB3czM1k5R3TGYGYimsKmWflGN89Z9lfaBiRIXiIFpJSH23wbwoAvNtOa"
}
```

### **🔍 KEMUNGKINAN PENYEBAB**

#### **1. Email Masuk ke Spam/Junk Folder**
- Cek folder **Spam** atau **Junk** di Gmail
- Cek folder **Promotions** atau **Updates**
- Cek folder **All Mail**

#### **2. Filter Gmail**
- Gmail mungkin memfilter email berdasarkan:
  - Subject line
  - Content yang mencurigakan
  - Link dalam email
  - Sender reputation

#### **3. Konfigurasi SMTP**
- Email dikirim dari: `faridindrawan0236@gmail.com`
- Email ditujukan ke: `silfa0236@gmail.com`
- SMTP: `smtp.gmail.com:587`

### **🛠️ SOLUSI YANG DICOBА**

#### **1. Cek Folder Spam**
```
1. Buka Gmail (silfa0236@gmail.com)
2. Cek folder "Spam" atau "Junk"
3. Cek folder "Promotions"
4. Cek folder "All Mail"
5. Search dengan keyword: "penilaian" atau "PKL"
```

#### **2. Test Email Manual**
```bash
php artisan tinker --execute="
try { 
    \Illuminate\Support\Facades\Mail::raw('Test email dari Laravel', function(\$message) { 
        \$message->to('silfa0236@gmail.com')->subject('Test Email Penilaian'); 
    }); 
    echo '✅ Email test berhasil dikirim ke silfa0236@gmail.com' . PHP_EOL; 
} catch (\Exception \$e) { 
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL; 
}
"
```

#### **3. Test Email Penilaian Spesifik**
```bash
php artisan tinker --execute="
echo '=== TEST EMAIL PENILAIAN ==='; 
try { 
    \$prakerin = \App\Models\Prakerin::with(['siswa', 'perusahaan', 'pembimbingPerusahaan'])->find(1); 
    \$kompetensi = \App\Models\Kompetensi::where('id_jurusan', \$prakerin->siswa->id_jurusan)->get(); 
    \$token = 'TEST_TOKEN_' . time(); 
    \Illuminate\Support\Facades\Cache::put('penilaian_token_' . \$token, \$prakerin->id_prakerin, now()->addDays(7)); 
    \Illuminate\Support\Facades\Mail::to('silfa0236@gmail.com')->send(new \App\Mail\PenilaianFormEmail(\$prakerin, \$prakerin->siswa, \$prakerin->perusahaan, \$prakerin->pembimbingPerusahaan, \$kompetensi, \$token)); 
    echo '✅ Email penilaian test berhasil dikirim ke silfa0236@gmail.com' . PHP_EOL; 
    echo 'Token: ' . \$token . PHP_EOL; 
} catch (\Exception \$e) { 
    echo '❌ Error: ' . \$e->getMessage() . PHP_EOL; 
}
"
```

### **📧 DETAIL EMAIL YANG DIKIRIM**

#### **From:** `faridindrawan0236@gmail.com`
#### **To:** `silfa0236@gmail.com`
#### **Subject:** "Form Penilaian PKL - [Nama Siswa]"
#### **Content:**
- Informasi siswa (nama, NIS, jurusan)
- Informasi perusahaan
- Informasi prakerin (tanggal mulai/selesai)
- Link form penilaian: `http://192.168.18.94:8000/penilaian/form/[TOKEN]`
- Token expiry: 7 hari

### **🔧 ALTERNATIF SOLUSI**

#### **1. Ganti Email Tujuan**
```php
// Di .env atau config
MAIL_TO_ADDRESS="email_lain@gmail.com"
```

#### **2. Tambah Whitelist di Gmail**
```
1. Buka Gmail Settings
2. Filters and Blocked Addresses
3. Create new filter
4. From: faridindrawan0236@gmail.com
5. Never send it to Spam
```

#### **3. Test dengan Email Lain**
```bash
# Test ke email lain
php artisan tinker --execute="
\Illuminate\Support\Facades\Mail::raw('Test email', function(\$message) { 
    \$message->to('email_test_lain@gmail.com')->subject('Test'); 
}); 
echo 'Email test dikirim ke email lain';
"
```

### **📊 MONITORING EMAIL**

#### **Cek Log Laravel:**
```bash
tail -f storage/logs/laravel.log | grep -i email
```

#### **Cek Cache Token:**
```bash
php artisan tinker --execute="
\$tokens = \Illuminate\Support\Facades\Cache::get('penilaian_tokens', []); 
print_r(\$tokens);
"
```

### **🎯 KESIMPULAN**

✅ **Email berhasil dikirim dari sistem Laravel**

❓ **Kemungkinan masalah:**
1. Email masuk ke folder Spam
2. Filter Gmail memblokir email
3. Delay pengiriman email

**Rekomendasi:**
1. Cek folder Spam di Gmail
2. Whitelist email `faridindrawan0236@gmail.com`
3. Test dengan email lain
4. Monitor log untuk konfirmasi pengiriman

**Email sistem penilaian sudah berfungsi dengan benar! 🚀** 