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

// Autoload models
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

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
    ->limit(5)
    ->get();

foreach ($prakerinData as $index => $prakerin) {
    echo "Record " . ($index + 1) . ":\n";
    echo "  ID Prakerin: " . $prakerin->id_prakerin . "\n";
    echo "  NIS Siswa: " . $prakerin->nis_siswa . "\n";
    echo "  NIP Pembimbing Sekolah: " . $prakerin->nip_pembimbing_sekolah . "\n";
    echo "  ID Pembimbing Perusahaan: " . $prakerin->id_pembimbing_perusahaan . "\n";
    echo "  Siswa exists: " . (!is_null($prakerin->siswa) ? 'Yes' : 'No') . "\n";
    echo "  Perusahaan exists: " . (!is_null($prakerin->perusahaan) ? 'Yes' : 'No') . "\n";
    echo "  Pembimbing Sekolah exists: " . (!is_null($prakerin->pembimbingSekolah) ? 'Yes' : 'No') . "\n";
    echo "  Pembimbing Perusahaan exists: " . (!is_null($prakerin->pembimbingPerusahaan) ? 'Yes' : 'No') . "\n";
    
    if ($prakerin->siswa) {
        echo "  Nama Siswa: " . $prakerin->siswa->nama_siswa . "\n";
        echo "  Kelas exists: " . (!is_null($prakerin->siswa->kelas) ? 'Yes' : 'No') . "\n";
        if ($prakerin->siswa->kelas) {
            echo "  Nama Kelas: " . $prakerin->siswa->kelas->nama_kelas . "\n";
        }
    }
    
    if ($prakerin->perusahaan) {
        echo "  Nama Perusahaan: " . $prakerin->perusahaan->nama_perusahaan . "\n";
    }
    
    if ($prakerin->pembimbingSekolah) {
        echo "  Nama Pembimbing Sekolah: " . $prakerin->pembimbingSekolah->nama_pembimbing_sekolah . "\n";
    }
    
    if ($prakerin->pembimbingPerusahaan) {
        echo "  Nama Pembimbing Perusahaan: " . $prakerin->pembimbingPerusahaan->nama . "\n";
    }
    
    echo str_repeat('-', 30) . "\n";
}

// Test 2: Check company data with Eloquent
echo "\nTest 2: Company data with Eloquent relations\n";
echo str_repeat('=', 50) . "\n";

$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->orderBy('id_perusahaan')
    ->limit(5)
    ->get();

foreach ($perusahaanData as $index => $perusahaan) {
    echo "Record " . ($index + 1) . ":\n";
    echo "  ID Perusahaan: " . $perusahaan->id_perusahaan . "\n";
    echo "  Nama Perusahaan: " . $perusahaan->nama_perusahaan . "\n";
    echo "  NIP Pembimbing Sekolah: " . $perusahaan->nip_pembimbing_sekolah . "\n";
    echo "  ID Pembimbing Perusahaan: " . $perusahaan->id_pembimbing_perusahaan . "\n";
    echo "  Pembimbing Sekolah exists: " . (!is_null($perusahaan->pembimbingSekolah) ? 'Yes' : 'No') . "\n";
    echo "  Pembimbing Perusahaan exists: " . (!is_null($perusahaan->pembimbingPerusahaan) ? 'Yes' : 'No') . "\n";
    
    if ($perusahaan->pembimbingSekolah) {
        echo "  Nama Pembimbing Sekolah: " . $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah . "\n";
    }
    
    if ($perusahaan->pembimbingPerusahaan && $perusahaan->pembimbingPerusahaan->isNotEmpty()) {\n        echo "  Nama Pembimbing Perusahaan: " . $perusahaan->pembimbingPerusahaan->first()->nama . "\n";\n    }
    
    echo str_repeat('-', 30) . "\n";
}