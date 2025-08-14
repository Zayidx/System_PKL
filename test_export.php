<?php
// File untuk memeriksa data export
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

// Setup Laravel Capsule
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'magang_db',
    'username'  => 'root',
    'password' => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Load models
require_once 'app/Models/Prakerin.php';
require_once 'app/Models/Siswa.php';
require_once 'app/Models/Kelas.php';
require_once 'app/Models/Perusahaan.php';
require_once 'app/Models/PembimbingSekolah.php';
require_once 'app/Models/PembimbingPerusahaan.php';
require_once 'app/Models/Pengajuan.php';

echo "Testing export data processing...\n";

// Simulate exportExcelPrakerin function
echo "\nSimulating exportExcelPrakerin function\n";
echo str_repeat('=', 50) . "\n";

// Ambil semua data prakerin dengan informasi siswa dan kelas
$prakerinData = \App\Models\Prakerin::with(['siswa.kelas', 'perusahaan', 'pembimbingSekolah', 'pembimbingPerusahaan'])
    ->orderBy('status_prakerin')
    ->get();

echo "Total prakerin records: " . $prakerinData->count() . "\n";

// Format data untuk Excel
$data = [];

// Header
$headers = [
    'No',
    'NIS',
    'Nama Siswa',
    'Kelas',
    'Perusahaan',
    'Pembimbing Sekolah',
    'Pembimbing Perusahaan',
    'Tanggal Mulai',
    'Tanggal Selesai',
    'Status Prakerin'
];

echo "\nProcessing data...\n";

// Data
foreach ($prakerinData as $index => $prakerin) {
    // Perbaiki cara menampilkan pembimbing sekolah
    $pembimbingSekolahNama = '-';
    if (!is_null($prakerin->pembimbingSekolah) && isset($prakerin->pembimbingSekolah->nama_pembimbing_sekolah)) {
        $pembimbingSekolahNama = $prakerin->pembimbingSekolah->nama_pembimbing_sekolah;
    }

    // Perbaiki cara menampilkan pembimbing perusahaan
    $pembimbingPerusahaanNama = '-';
    if (!is_null($prakerin->pembimbingPerusahaan) && isset($prakerin->pembimbingPerusahaan->nama)) {
        $pembimbingPerusahaanNama = $prakerin->pembimbingPerusahaan->nama;
    }

    // Perbaiki cara menampilkan data siswa
    $nisSiswa = '-';
    $namaSiswa = '-';
    $kelasSiswa = '-';
    
    if (!is_null($prakerin->siswa)) {
        $nisSiswa = $prakerin->siswa->nis ?? '-';
        $namaSiswa = $prakerin->siswa->nama_siswa ?? '-';
        
        if (!is_null($prakerin->siswa->kelas)) {
            $kelasSiswa = $prakerin->siswa->kelas->nama_kelas ?? '-';
        }
    }

    // Perbaiki cara menampilkan perusahaan
    $namaPerusahaan = '-';
    if (!is_null($prakerin->perusahaan)) {
        $namaPerusahaan = $prakerin->perusahaan->nama_perusahaan ?? '-';
    }

    $data[] = [
        $index + 1,
        $nisSiswa,
        $namaSiswa,
        $kelasSiswa,
        $namaPerusahaan,
        $pembimbingSekolahNama,
        $pembimbingPerusahaanNama,
        $prakerin->tanggal_mulai ? Carbon::parse($prakerin->tanggal_mulai)->format('d/m/Y') : '-',
        $prakerin->tanggal_selesai ? Carbon::parse($prakerin->tanggal_selesai)->format('d/m/Y') : '-',
        ucfirst($prakerin->status_prakerin) ?? '-'
    ];
    
    // Show first 3 records for debugging
    if ($index < 3) {
        echo "Record " . ($index + 1) . ":\n";
        echo "  NIS: " . $nisSiswa . "\n";
        echo "  Nama Siswa: " . $namaSiswa . "\n";
        echo "  Kelas: " . $kelasSiswa . "\n";
        echo "  Perusahaan: " . $namaPerusahaan . "\n";
        echo "  Pembimbing Sekolah: " . $pembimbingSekolahNama . "\n";
        echo "  Pembimbing Perusahaan: " . $pembimbingPerusahaanNama . "\n";
        echo "  Tanggal Mulai: " . ($prakerin->tanggal_mulai ? Carbon::parse($prakerin->tanggal_mulai)->format('d/m/Y') : '-') . "\n";
        echo "  Tanggal Selesai: " . ($prakerin->tanggal_selesai ? Carbon::parse($prakerin->tanggal_selesai)->format('d/m/Y') : '-') . "\n";
        echo "  Status: " . (ucfirst($prakerin->status_prakerin) ?? '-') . "\n";
        echo str_repeat('-', 30) . "\n";
    }
}

// Gabungkan header dan data
$exportData = array_merge([$headers], $data);

echo "\nHeader data:\n";
print_r($headers);

echo "\nFirst data row:\n";
print_r($exportData[1]);

echo "\nTotal rows in export data: " . count($exportData) . "\n";

// Simulate exportExcelPerusahaan function
echo "\n\nSimulating exportExcelPerusahaan function\n";
echo str_repeat('=', 50) . "\n";

// Ambil semua data perusahaan dengan relasi
$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->orderBy('nama_perusahaan')
    ->get();

echo "Total perusahaan records: " . $perusahaanData->count() . "\n";

// Format data untuk Excel
$dataPerusahaan = [];

// Header
$headersPerusahaan = [
    'No',
    'Nama Perusahaan',
    'Alamat',
    'Email',
    'Kontak',
    'Jumlah Siswa Diterima',
    'Pembimbing Sekolah',
    'Pembimbing Perusahaan'
];

echo "\nProcessing company data...\n";

// Data
foreach ($perusahaanData as $index => $perusahaan) {
    // Hitung jumlah siswa yang diterima di perusahaan ini
    $jumlahSiswa = \App\Models\Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
        ->where('status_pengajuan', 'diterima_perusahaan')
        ->count();

    // Perbaiki cara menampilkan pembimbing sekolah
    $pembimbingSekolahNama = '-';
    if (!is_null($perusahaan->pembimbingSekolah) && isset($perusahaan->pembimbingSekolah->nama_pembimbing_sekolah)) {
        $pembimbingSekolahNama = $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah;
    }

    // Perbaiki cara menampilkan pembimbing perusahaan
    $pembimbingPerusahaanNama = '-';
    if (!is_null($perusahaan->pembimbingPerusahaan) && $perusahaan->pembimbingPerusahaan->isNotEmpty()) {
        $pembimbingPerusahaanNama = $perusahaan->pembimbingPerusahaan->pluck('nama')->implode(', ');
    }

    $dataPerusahaan[] = [
        $index + 1,
        $perusahaan->nama_perusahaan ?? '-',
        $perusahaan->alamat_perusahaan ?? '-',
        $perusahaan->email_perusahaan ?? '-',
        $perusahaan->kontak_perusahaan ?? '-',
        $jumlahSiswa,
        $pembimbingSekolahNama,
        $pembimbingPerusahaanNama
    ];
    
    // Show first 3 records for debugging
    if ($index < 3) {
        echo "Record " . ($index + 1) . ":\n";
        echo "  Nama Perusahaan: " . ($perusahaan->nama_perusahaan ?? '-') . "\n";
        echo "  Alamat: " . ($perusahaan->alamat_perusahaan ?? '-') . "\n";
        echo "  Email: " . ($perusahaan->email_perusahaan ?? '-') . "\n";
        echo "  Kontak: " . ($perusahaan->kontak_perusahaan ?? '-') . "\n";
        echo "  Jumlah Siswa: " . $jumlahSiswa . "\n";
        echo "  Pembimbing Sekolah: " . $pembimbingSekolahNama . "\n";
        echo "  Pembimbing Perusahaan: " . $pembimbingPerusahaanNama . "\n";
        echo str_repeat('-', 30) . "\n";
    }
}

// Gabungkan header dan data
$exportDataPerusahaan = array_merge([$headersPerusahaan], $dataPerusahaan);

echo "\nHeader data perusahaan:\n";
print_r($headersPerusahaan);

echo "\nFirst data row perusahaan:\n";
print_r($exportDataPerusahaan[1]);

echo "\nTotal rows in export data perusahaan: " . count($exportDataPerusahaan) . "\n";