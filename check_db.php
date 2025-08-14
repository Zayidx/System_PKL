<?php
// Konfigurasi database dari .env
$host = '127.0.0.1';
$dbname = 'magang_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query untuk memeriksa data prakerin
    $stmt = $pdo->query("SELECT 
        p.id_prakerin,
        s.nama_siswa,
        k.nama_kelas,
        pr.nama_perusahaan,
        ps.nama_pembimbing_sekolah,
        pp.nama as nama_pembimbing_perusahaan,
        p.status_prakerin
    FROM prakerin p
    LEFT JOIN siswa s ON p.nis_siswa = s.nis
    LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN perusahaan pr ON p.id_perusahaan = pr.id_perusahaan
    LEFT JOIN pembimbing_sekolah ps ON p.nip_pembimbing_sekolah = ps.nip_pembimbing_sekolah
    LEFT JOIN pembimbing_perusahaan pp ON p.id_pembimbing_perusahaan = pp.id_pembimbing
    ORDER BY p.status_prakerin
    LIMIT 5");

    echo "Data Prakerin Sample:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id_prakerin'] . "\n";
        echo "Siswa: " . ($row['nama_siswa'] ?? 'N/A') . "\n";
        echo "Kelas: " . ($row['nama_kelas'] ?? 'N/A') . "\n";
        echo "Perusahaan: " . ($row['nama_perusahaan'] ?? 'N/A') . "\n";
        echo "Pembimbing Sekolah: " . ($row['nama_pembimbing_sekolah'] ?? 'N/A') . "\n";
        echo "Pembimbing Perusahaan: " . ($row['nama_pembimbing_perusahaan'] ?? 'N/A') . "\n";
        echo "Status: " . $row['status_prakerin'] . "\n";
        echo str_repeat("-", 30) . "\n";
    }

    // Query untuk memeriksa data perusahaan
    $stmt2 = $pdo->query("SELECT 
        pr.id_perusahaan,
        pr.nama_perusahaan,
        pr.alamat_perusahaan,
        ps.nama_pembimbing_sekolah,
        pp.nama as nama_pembimbing_perusahaan
    FROM perusahaan pr
    LEFT JOIN pembimbing_sekolah ps ON pr.nip_pembimbing_sekolah = ps.nip_pembimbing_sekolah
    LEFT JOIN pembimbing_perusahaan pp ON pr.id_pembimbing_perusahaan = pp.id_pembimbing
    ORDER BY pr.nama_perusahaan
    LIMIT 5");

    echo "\nData Perusahaan Sample:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id_perusahaan'] . "\n";
        echo "Perusahaan: " . ($row['nama_perusahaan'] ?? 'N/A') . "\n";
        echo "Alamat: " . ($row['alamat_perusahaan'] ?? 'N/A') . "\n";
        echo "Pembimbing Sekolah: " . ($row['nama_pembimbing_sekolah'] ?? 'N/A') . "\n";
        echo "Pembimbing Perusahaan: " . ($row['nama_pembimbing_perusahaan'] ?? 'N/A') . "\n";
        echo str_repeat("-", 30) . "\n";
    }

} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage() . "\n";
}