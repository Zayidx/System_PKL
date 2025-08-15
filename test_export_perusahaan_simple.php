<?php
// File untuk test export perusahaan lengkap
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

echo "Testing exportExcelPerusahaan function...\n";

// Simulate exportExcelPerusahaan function
// Ambil semua data perusahaan dengan relasi
$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->orderBy('nama_perusahaan')
    ->limit(5)
    ->get();

echo "Total perusahaan (limited to 5): " . $perusahaanData->count() . "\n";

// Data
foreach ($perusahaanData as $index => $perusahaan) {
    // Hitung jumlah siswa yang diterima di perusahaan ini
    $jumlahSiswa = \App\Models\Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
        ->where('status_pengajuan', 'diterima_perusahaan')
        ->count();

    echo "\nPerusahaan " . ($index + 1) . ":\n";
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
        $index + 1,
        $perusahaan->nama_perusahaan ?? '-',
        $perusahaan->alamat_perusahaan ?? '-',
        $perusahaan->email_perusahaan ?? '-',
        $perusahaan->kontak_perusahaan ?? '-',
        $jumlahSiswa,
        $pembimbingSekolahNama,
        $pembimbingPerusahaanNama
    ];

    echo "  Row Data: ";
    print_r($rowData);
}