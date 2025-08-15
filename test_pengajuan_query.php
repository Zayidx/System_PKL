<?php
// File untuk test query pengajuan dalam fungsi export
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
require_once 'app/Models/Perusahaan.php';
require_once 'app/Models/Pengajuan.php';

echo "Testing pengajuan count query...\n";

// Ambil semua data perusahaan
$perusahaanData = \App\Models\Perusahaan::orderBy('id_perusahaan')->get();

echo "Total perusahaan: " . $perusahaanData->count() . "\n";

foreach ($perusahaanData as $index => $perusahaan) {
    // Hitung jumlah siswa yang diterima di perusahaan ini menggunakan query Eloquent
    $jumlahSiswa = \App\Models\Pengajuan::where('id_perusahaan', $perusahaan->id_perusahaan)
        ->where('status_pengajuan', 'diterima_perusahaan')
        ->count();
    
    echo "Perusahaan ID: " . $perusahaan->id_perusahaan . " - " . $perusahaan->nama_perusahaan . "\n";
    echo "  Jumlah siswa diterima (Eloquent): " . $jumlahSiswa . "\n";
    
    // Bandingkan dengan query SQL langsung
    $sqlQuery = "SELECT COUNT(*) as jumlah FROM pengajuan WHERE id_perusahaan = " . $perusahaan->id_perusahaan . " AND status_pengajuan = 'diterima_perusahaan'";
    echo "  SQL Query: " . $sqlQuery . "\n";
    
    // Hentikan setelah 5 perusahaan untuk debugging
    if ($index >= 5) {
        break;
    }
}