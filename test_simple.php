<?php
// File sederhana untuk memeriksa data menggunakan Eloquent
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

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

echo "Testing Eloquent data retrieval...\n";

// Test 1: Check prakerin data with Eloquent
echo "\nTest 1: Prakerin data with Eloquent relations\n";
echo str_repeat('=', 50) . "\n";

$prakerinData = \App\Models\Prakerin::with(['siswa.kelas', 'perusahaan', 'pembimbingSekolah', 'pembimbingPerusahaan'])
    ->orderBy('id_prakerin')
    ->limit(3)
    ->get();

foreach ($prakerinData as $index => $prakerin) {
    echo "Record " . ($index + 1) . ":\n";
    echo "  ID Prakerin: " . $prakerin->id_prakerin . "\n";
    echo "  Siswa: " . ($prakerin->siswa ? $prakerin->siswa->nama_siswa : 'N/A') . "\n";
    echo "  Perusahaan: " . ($prakerin->perusahaan ? $prakerin->perusahaan->nama_perusahaan : 'N/A') . "\n";
    echo "  Pembimbing Sekolah: " . ($prakerin->pembimbingSekolah ? $prakerin->pembimbingSekolah->nama_pembimbing_sekolah : 'N/A') . "\n";
    echo "  Pembimbing Perusahaan: " . ($prakerin->pembimbingPerusahaan ? $prakerin->pembimbingPerusahaan->nama : 'N/A') . "\n";
    echo str_repeat('-', 30) . "\n";
}

// Test 2: Check company data with Eloquent
echo "\nTest 2: Company data with Eloquent relations\n";
echo str_repeat('=', 50) . "\n";

$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->orderBy('id_perusahaan')
    ->limit(3)
    ->get();

foreach ($perusahaanData as $index => $perusahaan) {
    echo "Record " . ($index + 1) . ":\n";
    echo "  ID Perusahaan: " . $perusahaan->id_perusahaan . "\n";
    echo "  Nama Perusahaan: " . $perusahaan->nama_perusahaan . "\n";
    echo "  Pembimbing Sekolah: " . ($perusahaan->pembimbingSekolah ? $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah : 'N/A') . "\n";
    
    // Handle pembimbing perusahaan (HasMany relation)
    if ($perusahaan->pembimbingPerusahaan && $perusahaan->pembimbingPerusahaan->isNotEmpty()) {
        $pembimbingNames = $perusahaan->pembimbingPerusahaan->pluck('nama')->implode(', ');
        echo "  Pembimbing Perusahaan: " . $pembimbingNames . "\n";
    } else {
        echo "  Pembimbing Perusahaan: N/A\n";
    }
    echo str_repeat('-', 30) . "\n";
}