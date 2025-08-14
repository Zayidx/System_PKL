<?php
// File untuk test export prakerin dengan multiple prakerin per siswa
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
require_once 'app/Models/Siswa.php';
require_once 'app/Models/Kelas.php';
require_once 'app/Models/Prakerin.php';
require_once 'app/Models/Perusahaan.php';
require_once 'app/Models/PembimbingSekolah.php';
require_once 'app/Models/PembimbingPerusahaan.php';

echo "Testing new exportExcelPrakerin function with multiple prakerin per student...\n";

// Simulate the new exportExcelPrakerin function
// Ambil semua data siswa dengan informasi prakerin lengkap
$siswaData = \App\Models\Siswa::with(['kelas', 'prakerin.perusahaan', 'prakerin.pembimbingSekolah', 'prakerin.pembimbingPerusahaan'])
    ->whereHas('prakerin')
    ->orderBy('nama_siswa')
    ->get();

echo "Total students with prakerin: " . $siswaData->count() . "\n";

// Format data untuk Excel
$data = [];

// Header dasar
$headers = [
    'No',
    'NIS',
    'Nama Siswa',
    'Kelas'
];

// Tentukan jumlah maksimum prakerin per siswa untuk menentukan jumlah kolom
$maxPrakerin = 1;
foreach ($siswaData as $siswa) {
    if ($siswa->prakerin && $siswa->prakerin->count() > $maxPrakerin) {
        $maxPrakerin = $siswa->prakerin->count();
    }
}

echo "Maximum prakerin per student: " . $maxPrakerin . "\n";

// Tambahkan header untuk setiap prakerin
for ($i = 1; $i <= $maxPrakerin; $i++) {
    $headers = array_merge($headers, [
        "Perusahaan $i",
        "Pembimbing Sekolah $i",
        "Pembimbing Perusahaan $i",
        "Tanggal Mulai $i",
        "Tanggal Selesai $i",
        "Status Prakerin $i"
    ]);
}

echo "\nHeaders:\n";
print_r($headers);

echo "\nProcessing data...\n";

// Data
foreach ($siswaData as $index => $siswa) {
    $rowData = [
        $index + 1,
        $siswa->nis ?? '-',
        $siswa->nama_siswa ?? '-',
        $siswa->kelas ? $siswa->kelas->nama_kelas : '-'
    ];

    // Tambahkan informasi untuk setiap prakerin
    $prakerinCount = 0;
    if ($siswa->prakerin) {
        foreach ($siswa->prakerin as $prakerin) {
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

            // Perbaiki cara menampilkan perusahaan
            $namaPerusahaan = '-';
            if (!is_null($prakerin->perusahaan)) {
                $namaPerusahaan = $prakerin->perusahaan->nama_perusahaan ?? '-';
            }

            $rowData = array_merge($rowData, [
                $namaPerusahaan,
                $pembimbingSekolahNama,
                $pembimbingPerusahaanNama,
                $prakerin->tanggal_mulai ? Carbon::parse($prakerin->tanggal_mulai)->format('d/m/Y') : '-',
                $prakerin->tanggal_selesai ? Carbon::parse($prakerin->tanggal_selesai)->format('d/m/Y') : '-',
                ucfirst($prakerin->status_prakerin) ?? '-'
            ]);
            
            $prakerinCount++;
        }
    }

    // Tambahkan kolom kosong untuk prakerin yang tidak ada
    for ($i = $prakerinCount; $i < $maxPrakerin; $i++) {
        $rowData = array_merge($rowData, [
            '-', '-', '-', '-', '-', '-'
        ]);
    }

    $data[] = $rowData;
    
    // Show first 3 students for debugging
    if ($index < 3) {
        echo "Student " . ($index + 1) . " (" . $siswa->nama_siswa . "):\n";
        echo "  NIS: " . ($siswa->nis ?? '-') . "\n";
        echo "  Kelas: " . ($siswa->kelas ? $siswa->kelas->nama_kelas : '-') . "\n";
        echo "  Number of prakerin: " . ($siswa->prakerin ? $siswa->prakerin->count() : 0) . "\n";
        
        if ($siswa->prakerin) {
            foreach ($siswa->prakerin as $idx => $prakerin) {
                echo "  Prakerin " . ($idx + 1) . ":\n";
                echo "    Perusahaan: " . ($prakerin->perusahaan ? $prakerin->perusahaan->nama_perusahaan : '-') . "\n";
                echo "    Pembimbing Sekolah: " . ($prakerin->pembimbingSekolah ? $prakerin->pembimbingSekolah->nama_pembimbing_sekolah : '-') . "\n";
                echo "    Pembimbing Perusahaan: " . ($prakerin->pembimbingPerusahaan ? $prakerin->pembimbingPerusahaan->nama : '-') . "\n";
            }
        }
        echo str_repeat('-', 40) . "\n";
    }
}

// Gabungkan header dan data
$exportData = array_merge([$headers], $data);

echo "\nFirst data row:\n";
print_r($exportData[1]);

echo "\nTotal rows in export data: " . count($exportData) . "\n";
echo "Total columns: " . count($exportData[0]) . "\n";