<?php
// File untuk membuat file Excel test yang sebenarnya
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

use Maatwebsite\Excel\Facades\Excel;

echo "Creating actual Excel file...\n";

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

// Buat file Excel
$filename = 'actual_test_export_perusahaan_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

try {
    // Simpan file Excel
    $exporter = new \App\Exports\DataExport($exportData);
    
    // Karena kita tidak bisa menggunakan Excel::download() di command line,
    // kita akan menggunakan PhpOffice\PhpSpreadsheet langsung
    $arrayData = $exporter->array();
    $headings = $exporter->headings();
    
    // Buat spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Tambahkan headings
    $col = 'A';
    foreach ($headings as $heading) {
        $sheet->setCellValue($col . '1', $heading);
        $col++;
    }
    
    // Tambahkan data
    for ($row = 0; $row < count($arrayData); $row++) {
        $col = 'A';
        for ($colIndex = 0; $colIndex < count($arrayData[$row]); $colIndex++) {
            $sheet->setCellValue($col . ($row + 2), $arrayData[$row][$colIndex]);
            $col++;
        }
    }
    
    // Apply styles
    $styles = $exporter->styles($sheet);
    foreach ($styles as $rowNumber => $style) {
        $sheet->getStyle('A' . $rowNumber . ':' . $col . $rowNumber)->applyFromArray($style);
    }
    
    // Apply column formats
    $columnFormats = $exporter->columnFormats();
    foreach ($columnFormats as $column => $format) {
        $sheet->getStyle($column . ':' . $column)->getNumberFormat()->setFormatCode($format);
    }
    
    // Simpan file
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($filename);
    
    echo "Excel file created successfully: " . $filename . "\n";
    echo "Total rows: " . (count($arrayData) + 1) . " (including header)\n";
    echo "Total columns: " . count($headings) . "\n";
    
} catch (Exception $e) {
    echo "Error creating Excel file: " . $e->getMessage() . "\n";
}