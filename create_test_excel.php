<?php
// File untuk test export Excel lengkap
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

// Load models and export class
require_once 'app/Models/Perusahaan.php';
require_once 'app/Models/PembimbingSekolah.php';
require_once 'app/Models/PembimbingPerusahaan.php';
require_once 'app/Models/Pengajuan.php';
require_once 'app/Exports/DataExport.php';

echo "Creating test Excel file...\n";

// Simulate export data
$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->orderBy('nama_perusahaan')
    ->get();

// Format data untuk Excel
$data = [];

// Header
$headers = [
    'No',
    'Nama Perusahaan',
    'Alamat',
    'Email',
    'Kontak',
    'Jumlah Siswa Diterima',
    'Pembimbing Sekolah',
    'Pembimbing Perusahaan'
];

// Data
foreach ($perusahaanData as $index => $perusahaan) {
    // Hitung jumlah siswa yang diterima di perusahaan ini
    $jumlahSiswa = \App\Models\Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
        ->where('status_pengajuan', 'diterima_perusahaan')
        ->count();

    $data[] = [
        $index + 1,
        $perusahaan->nama_perusahaan ?? '-',
        $perusahaan->alamat_perusahaan ?? '-',
        $perusahaan->email_perusahaan ?? '-',
        $perusahaan->kontak_perusahaan ?? '-',
        (int)$jumlahSiswa,  // Ensure it's an integer
        $perusahaan->pembimbingSekolah ? $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah : '-',
        $perusahaan->pembimbingPerusahaan && $perusahaan->pembimbingPerusahaan->isNotEmpty() ? 
            $perusahaan->pembimbingPerusahaan->pluck('nama')->implode(', ') : '-'
    ];
}

// Gabungkan header dan data
$exportData = array_merge([$headers], $data);

// Test DataExport class
$exporter = new \App\Exports\DataExport($exportData);

// Buat file Excel test
$filename = 'test_export_perusahaan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

echo "Test file created: " . $filename . "\n";
echo "Total rows: " . count($exportData) . "\n";
echo "Headers: " . implode(', ', $headers) . "\n";

// Tampilkan beberapa baris pertama untuk verifikasi
echo "\nFirst 3 data rows:\n";
for ($i = 1; $i <= min(3, count($exportData)-1); $i++) {
    echo "Row $i: ";
    print_r($exportData[$i]);
}