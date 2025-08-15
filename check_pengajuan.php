<?php
// Konfigurasi database dari .env
$host = '127.0.0.1';
$dbname = 'magang_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query untuk memeriksa data pengajuan
    $stmt = $pdo->query("SELECT 
        p.id_pengajuan,
        p.nis_siswa,
        s.nama_siswa,
        p.id_perusahaan,
        pr.nama_perusahaan,
        p.status_pengajuan
    FROM pengajuan p
    LEFT JOIN siswa s ON p.nis_siswa = s.nis
    LEFT JOIN perusahaan pr ON p.id_perusahaan = pr.id_perusahaan
    ORDER BY p.id_pengajuan
    LIMIT 20");

    echo "Data Pengajuan Sample:\n";
    echo str_repeat("=", 80) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID Pengajuan: " . $row['id_pengajuan'] . "\n";
        echo "NIS Siswa: " . ($row['nis_siswa'] ?? 'NULL') . "\n";
        echo "Nama Siswa: " . ($row['nama_siswa'] ?? 'NULL') . "\n";
        echo "ID Perusahaan: " . ($row['id_perusahaan'] ?? 'NULL') . "\n";
        echo "Nama Perusahaan: " . ($row['nama_perusahaan'] ?? 'NULL') . "\n";
        echo "Status Pengajuan: " . ($row['status_pengajuan'] ?? 'NULL') . "\n";
        echo str_repeat("-", 50) . "\n";
    }

    // Query untuk menghitung jumlah pengajuan berdasarkan status
    $stmt2 = $pdo->query("SELECT 
        status_pengajuan,
        COUNT(*) as jumlah
    FROM pengajuan
    GROUP BY status_pengajuan");

    echo "\nJumlah Pengajuan Berdasarkan Status:\n";
    echo str_repeat("=", 50) . "\n";
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "Status: " . ($row['status_pengajuan'] ?? 'NULL') . " - Jumlah: " . $row['jumlah'] . "\n";
    }

    // Query khusus untuk pengajuan diterima
    $stmt3 = $pdo->query("SELECT 
        p.id_perusahaan,
        pr.nama_perusahaan,
        COUNT(*) as jumlah_siswa_diterima
    FROM pengajuan p
    LEFT JOIN perusahaan pr ON p.id_perusahaan = pr.id_perusahaan
    WHERE p.status_pengajuan = 'diterima_perusahaan'
    GROUP BY p.id_perusahaan, pr.nama_perusahaan
    ORDER BY jumlah_siswa_diterima DESC");

    echo "\nJumlah Siswa Diterima per Perusahaan:\n";
    echo str_repeat("=", 50) . "\n";
    while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        echo "Perusahaan: " . ($row['nama_perusahaan'] ?? 'NULL') . " - Jumlah: " . $row['jumlah_siswa_diterima'] . "\n";
    }

} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage() . "\n";
}