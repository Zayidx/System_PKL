# CRUD Kompetensi PKL - Superadmin

## **🎯 FITUR YANG DIBUAT**

### **1. Komponen Livewire: `Administrator/DasborKompetensiNilai`**

#### **✅ Fitur Utama:**
- **CRUD Lengkap:** Create, Read, Update, Delete kompetensi
- **Search & Filter:** Pencarian berdasarkan nama kompetensi atau jurusan
- **Pagination:** 10 item per halaman
- **Modal Interface:** Form tambah/edit dalam modal
- **Konfirmasi Hapus:** Modal konfirmasi sebelum hapus
- **Validasi:** Server-side validation dengan pesan error
- **Logging:** Log semua aktivitas CRUD
- **Sweet Alert:** Notifikasi sukses/error

#### **🔧 Keamanan:**
- **Role-based Access:** Hanya superadmin yang bisa akses
- **Validation:** Validasi input yang ketat
- **Foreign Key Protection:** Tidak bisa hapus kompetensi yang sudah digunakan

### **2. Route & Navigation**

#### **✅ Route:**
```php
// routes/web.php
Route::prefix('administrator')->name('administrator.')->middleware('role:superadmin')->group(function () {
    Route::prefix('data-induk')->name('data.')->group(function () {
        Route::get('/kompetensi', DasborKompetensiNilai::class)->name('kompetensi');
    });
});
```

#### **✅ Sidebar Menu:**
```html
<!-- resources/views/components/layouts/partials/sidebar-admin-dashboard.blade.php -->
<li class="submenu-item {{ Request::routeIs('administrator.data.kompetensi') ? 'active' : '' }}">
    <a href="{{ route('administrator.data.kompetensi') }}" wire:navigate class="submenu-link">
        <i class="bi bi-list-check me-2"></i>
        Pengelolaan Kompetensi PKL
    </a>
</li>
```

## **🚀 CARA PENGGUNAAN**

### **1. Akses Halaman**
```
URL: http://192.168.18.94:8000/administrator/data-induk/kompetensi
Login: superadmin@sekolah.sch.id / password
```

### **2. Fitur CRUD**

#### **📝 Tambah Kompetensi Baru:**
1. Klik tombol "Tambah Kompetensi"
2. Isi form:
   - **Nama Kompetensi:** Wajib diisi (max 255 karakter)
   - **Jurusan:** Pilih dari dropdown (RPL/TKJ/DKV)
3. Klik "Simpan"
4. Sweet alert konfirmasi sukses

#### **✏️ Edit Kompetensi:**
1. Klik tombol edit (ikon pensil) pada baris kompetensi
2. Modal edit akan terbuka dengan data yang sudah terisi
3. Ubah data yang diperlukan
4. Klik "Update"
5. Sweet alert konfirmasi sukses

#### **🗑️ Hapus Kompetensi:**
1. Klik tombol hapus (ikon trash) pada baris kompetensi
2. Modal konfirmasi akan terbuka
3. **Peringatan:** Jika kompetensi sudah digunakan dalam penilaian, tombol hapus akan disabled
4. Klik "Hapus" untuk konfirmasi
5. Sweet alert konfirmasi sukses

#### **🔍 Search & Filter:**
1. Gunakan search bar untuk mencari kompetensi
2. Pencarian berdasarkan:
   - Nama kompetensi
   - Nama jurusan (lengkap/singkat)
3. Hasil pencarian real-time

## **📊 INTERFACE DETAILS**

### **1. Tabel Kompetensi**
```
| No | Kompetensi | Jurusan | Jumlah Penilaian | Dibuat | Aksi |
|----|------------|---------|------------------|--------|------|
| 1  | Pemrograman Web Dasar | RPL | 5 penilaian | 05 Aug 2025 | [Edit] [Hapus] |
```

### **2. Kolom Informasi:**
- **No:** Nomor urut dengan pagination
- **Kompetensi:** Nama kompetensi (bold)
- **Jurusan:** Badge jurusan + nama lengkap
- **Jumlah Penilaian:** Badge dengan warna (hijau jika ada penilaian)
- **Dibuat:** Tanggal pembuatan kompetensi
- **Aksi:** Tombol edit dan hapus

### **3. Modal Form:**
```html
<!-- Modal Tambah/Edit -->
- Nama Kompetensi (required, max 255)
- Jurusan (required, dropdown)
- Info: Peringatan jika edit kompetensi yang sudah digunakan
```

### **4. Modal Konfirmasi Hapus:**
```html
<!-- Modal Konfirmasi -->
- Nama kompetensi yang akan dihapus
- Jurusan kompetensi
- Peringatan jika ada penilaian yang menggunakan kompetensi
- Tombol Batal dan Hapus
```

## **🔧 TEKNIS IMPLEMENTASI**

### **1. Komponen Livewire**
```php
// app/Livewire/Administrator/DasborKompetensiNilai.php
class KompetensiNilaiDashboard extends Component
{
    use WithPagination;
    
    // Properties
    public $search = '';
    public $showModal = false;
    public $editingKompetensi = null;
    public $nama_kompetensi = '';
    public $id_jurusan = '';
    public $confirmingDelete = false;
    public $kompetensiToDelete = null;
    
    // Methods
    public function mount() // Cek role superadmin
    public function openModal() // Buka modal tambah
    public function closeModal() // Tutup modal
    public function editKompetensi($id) // Edit kompetensi
    public function saveKompetensi() // Simpan/update
    public function confirmDelete($id) // Konfirmasi hapus
    public function deleteKompetensi() // Hapus kompetensi
    public function render() // Render view dengan data
}
```

### **2. Validasi**
```php
protected $rules = [
    'nama_kompetensi' => 'required|string|max:255',
    'id_jurusan' => 'required|exists:jurusan,id_jurusan'
];

protected $messages = [
    'nama_kompetensi.required' => 'Nama kompetensi wajib diisi.',
    'nama_kompetensi.max' => 'Nama kompetensi maksimal 255 karakter.',
    'id_jurusan.required' => 'Jurusan wajib dipilih.',
    'id_jurusan.exists' => 'Jurusan yang dipilih tidak valid.'
];
```

### **3. Logging**
```php
// Log untuk setiap operasi CRUD
Log::info('Kompetensi berhasil dibuat', [
    'nama_kompetensi' => $this->nama_kompetensi,
    'id_jurusan' => $this->id_jurusan,
    'user_id' => auth()->id()
]);
```

### **4. Security Check**
```php
public function mount()
{
    // Cek apakah user adalah superadmin
    if (!auth()->check() || auth()->user()->role->name !== 'superadmin') {
        abort(403, 'Unauthorized access.');
    }
}
```

## **🎨 UI/UX FEATURES**

### **1. Responsive Design**
- **Mobile-friendly:** Tabel responsive dengan horizontal scroll
- **Bootstrap 5:** Menggunakan komponen Bootstrap terbaru
- **Bootstrap Icons:** Icon yang konsisten

### **2. User Experience**
- **Loading States:** Spinner saat proses save/delete
- **Real-time Search:** Pencarian tanpa reload halaman
- **Sweet Alert:** Notifikasi yang user-friendly
- **Modal Interface:** Form dalam modal yang clean

### **3. Data Protection**
- **Foreign Key Check:** Tidak bisa hapus kompetensi yang sudah digunakan
- **Validation:** Server-side validation yang ketat
- **Error Handling:** Error handling yang comprehensive

## **📈 MONITORING & ANALYTICS**

### **1. Logging System**
```php
// Semua operasi CRUD di-log
Log::info('Kompetensi berhasil dibuat', [...]);
Log::info('Kompetensi berhasil diupdate', [...]);
Log::info('Kompetensi berhasil dihapus', [...]);
Log::error('Error menyimpan kompetensi', [...]);
```

### **2. Usage Statistics**
- **Jumlah Penilaian:** Menampilkan berapa kali kompetensi digunakan
- **Created Date:** Tanggal pembuatan kompetensi
- **Search Analytics:** Bisa track pencarian yang sering dilakukan

## **🔍 TROUBLESHOOTING**

### **1. Modal Tidak Muncul**
- **Penyebab:** JavaScript error atau Bootstrap tidak load
- **Solusi:** Cek console browser, pastikan Bootstrap JS ter-load

### **2. Validasi Error**
- **Penyebab:** Input tidak sesuai rules
- **Solusi:** Cek pesan error di bawah input field

### **3. Tidak Bisa Hapus**
- **Penyebab:** Kompetensi sudah digunakan dalam penilaian
- **Solusi:** Tombol hapus akan disabled, hapus penilaian terlebih dahulu

### **4. Access Denied**
- **Penyebab:** User bukan superadmin
- **Solusi:** Login dengan akun superadmin

## **✅ KESIMPULAN**

### **🎯 SISTEM CRUD KOMPETENSI BERHASIL DIBUAT:**

1. **✅ Komponen Livewire Lengkap**
   - CRUD operations dengan modal interface
   - Search & pagination
   - Validation & error handling

2. **✅ Route & Navigation**
   - Route dengan middleware superadmin
   - Menu di sidebar admin
   - Active state untuk menu

3. **✅ Security & Validation**
   - Role-based access control
   - Server-side validation
   - Foreign key protection

4. **✅ User Experience**
   - Responsive design
   - Loading states
   - Sweet alert notifications
   - Real-time search

**Sistem CRUD Kompetensi siap digunakan oleh superadmin! 🚀** 
