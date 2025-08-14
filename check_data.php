<?php
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup Laravel Capsule
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'qiapkl',
    'username'  => 'root',
    'password'  => '',
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

// Check prakerin data
$prakerinData = \App\Models\Prakerin::with(['siswa.kelas', 'perusahaan', 'pembimbingSekolah', 'pembimbingPerusahaan'])
    ->orderBy('status_prakerin')
    ->get();

echo "Total prakerin records: " . $prakerinData->count() . "\n";
echo "Showing first 5 records:\n";

foreach ($prakerinData->take(5) as $index => $prakerin) {
    echo ($index + 1) . ". Siswa: " . ($prakerin->siswa ? $prakerin->siswa->nama_siswa : 'N/A') . "\n";
    echo "   Kelas: " . ($prakerin->siswa && $prakerin->siswa->kelas ? $prakerin->siswa->kelas->nama_kelas : 'N/A') . "\n";
    echo "   Perusahaan: " . ($prakerin->perusahaan ? $prakerin->perusahaan->nama_perusahaan : 'N/A') . "\n";
    echo "   Pembimbing Sekolah: " . ($prakerin->pembimbingSekolah ? $prakerin->pembimbingSekolah->nama_pembimbing_sekolah : 'N/A') . "\n";
    echo "   Pembimbing Perusahaan: " . ($prakerin->pembimbingPerusahaan ? $prakerin->pembimbingPerusahaan->nama : 'N/A') . "\n";
    echo "   Status: " . $prakerin->status_prakerin . "\n\n";
}

// Check company data
$perusahaanData = \App\Models\Perusahaan::with(['pembimbingPerusahaan', 'pembimbingSekolah'])
    ->orderBy('nama_perusahaan')
    ->get();

echo "Total perusahaan records: " . $perusahaanData->count() . "\n";
echo "Showing first 5 records:\n";

foreach ($perusahaanData->take(5) as $index => $perusahaan) {
    echo ($index + 1) . ". Perusahaan: " . ($perusahaan->nama_perusahaan ?? 'N/A') . "\n";
    echo "   Pembimbing Sekolah: " . ($perusahaan->pembimbingSekolah ? $perusahaan->pembimbingSekolah->nama_pembimbing_sekolah : 'N/A') . "\n";
    echo "   Pembimbing Perusahaan: " . ($perusahaan->pembimbingPerusahaan && $perusahaan->pembimbingPerusahaan->isNotEmpty() ? $perusahaan->pembimbingPerusahaan->pluck('nama')->implode(', ') : 'N/A') . "\n\n";
}