<?php
// File untuk test export perusahaan lengkap dengan fokus pada perusahaan ID 1
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
require_once 'app/Models/Perusahaan.php';
require_once 'app/Models/PembimbingSekolah.php';
require_once 'app/Models/PembimbingPerusahaan.php';
require_once 'app/Models/Pengajuan.php';

echo "Testing exportExcelPerusahaan function with focus on perusahaan ID 1...\n";

// Test specifically for perusahaan ID 1
$perusahaan = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->find(1);

if ($perusahaan) {
    echo "Perusahaan ID 1 (" . $perusahaan->nama_perusahaan . "):\n";
    
    // Hitung jumlah siswa yang diterima di perusahaan ini
    $jumlahSiswa = \App\Models\Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
        ->where('status_pengajuan', 'diterima_perusahaan')
        ->count();

    echo "  ID: " . $perusahaan->id_perusahaan . "\n";
    echo "  Nama: " . $perusahaan->nama_perusahaan . "\n";
    echo "  Jumlah Siswa Diterima: " . $jumlahSiswa . "\n";

    // Perbaiki cara menampilkan pembimbing sekolah
    $pembimbingSekolahNama = '-';
    if (!is_null($perusahaan->pembimbingSekolah) && isset($perusahaan->pembimbingSekolah->nama_pembimbing_sekolah)) {
        $pembimbingSekolahNama = $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah;
        echo "  Pembimbing Sekolah: " . $pembimbingSekolahNama . "\n";
    } else {
        echo "  Pembimbing Sekolah: - (tidak ada relasi)\n";
    }

    // Perbaiki cara menampilkan pembimbing perusahaan
    $pembimbingPerusahaanNama = '-';
    if (!is_null($perusahaan->pembimbingPerusahaan) && $perusahaan->pembimbingPerusahaan->isNotEmpty()) {
        $pembimbingPerusahaanNama = $perusahaan->pembimbingPerusahaan->pluck('nama')->implode(', ');
        echo "  Pembimbing Perusahaan: " . $pembimbingPerusahaanNama . "\n";
    } else {
        echo "  Pembimbing Perusahaan: - (tidak ada relasi)\n";
    }

    $rowData = [
        1,
        $perusahaan->nama_perusahaan ?? '-',
        $perusahaan->alamat_perusahaan ?? '-',
        $perusahaan->email_perusahaan ?? '-',
        $perusahaan->kontak_perusahaan ?? '-',
        $jumlahSiswa,
        $pembimbingSekolahNama,
        $pembimbingPerusahaanNama
    ];

    echo "  Row Data (should show jumlah siswa = 1): ";
    print_r($rowData);
} else {
    echo "Perusahaan ID 1 tidak ditemukan!\n";
}

// Test dengan pendekatan yang sama seperti fungsi export
echo "\n\nTesting with same approach as export function:\n";
$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->where('id_perusahaan', 1)
    ->get();

foreach ($perusahaanData as $perusahaan) {
    // Hitung jumlah siswa yang diterima di perusahaan ini
    $jumlahSiswa = \App\Models\Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
        ->where('status_pengajuan', 'diterima_perusahaan')
        ->count();

    echo "Perusahaan (ID " . $perusahaan->id_perusahaan . "): " . $perusahaan->nama_perusahaan . "\n";
    echo "  Jumlah Siswa Diterima: " . $jumlahSiswa . "\n";
}