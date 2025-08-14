<?php
// Konfigurasi database dari .env
$host = '127.0.0.1';
$dbname = 'magang_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query untuk memeriksa data prakerin dengan detail
    $stmt = $pdo->query("SELECT 
        p.id_prakerin,
        p.nis_siswa,
        p.nip_pembimbing_sekolah,
        p.id_pembimbing_perusahaan,
        p.id_perusahaan,
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
    ORDER BY p.id_prakerin
    LIMIT 10");

    echo "Detailed Prakerin Data:\n";
    echo str_repeat("=", 80) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID Prakerin: " . $row['id_prakerin'] . "\n";
        echo "NIS Siswa: " . ($row['nis_siswa'] ?? 'NULL') . "\n";
        echo "NIP Pembimbing Sekolah: " . ($row['nip_pembimbing_sekolah'] ?? 'NULL') . "\n";
        echo "ID Pembimbing Perusahaan: " . ($row['id_pembimbing_perusahaan'] ?? 'NULL') . "\n";
        echo "ID Perusahaan: " . ($row['id_perusahaan'] ?? 'NULL') . "\n";
        echo "Nama Siswa: " . ($row['nama_siswa'] ?? 'NULL') . "\n";
        echo "Kelas: " . ($row['nama_kelas'] ?? 'NULL') . "\n";
        echo "Perusahaan: " . ($row['nama_perusahaan'] ?? 'NULL') . "\n";
        echo "Pembimbing Sekolah: " . ($row['nama_pembimbing_sekolah'] ?? 'NULL') . "\n";
        echo "Pembimbing Perusahaan: " . ($row['nama_pembimbing_perusahaan'] ?? 'NULL') . "\n";
        echo "Status: " . $row['status_prakerin'] . "\n";
        echo str_repeat("-", 50) . "\n";
    }

    // Query untuk memeriksa data perusahaan dengan detail
    $stmt2 = $pdo->query("SELECT 
        pr.id_perusahaan,
        pr.nama_perusahaan,
        pr.nip_pembimbing_sekolah,
        pr.id_pembimbing_perusahaan,
        ps.nama_pembimbing_sekolah,
        pp.nama as nama_pembimbing_perusahaan
    FROM perusahaan pr
    LEFT JOIN pembimbing_sekolah ps ON pr.nip_pembimbing_sekolah = ps.nip_pembimbing_sekolah
    LEFT JOIN pembimbing_perusahaan pp ON pr.id_pembimbing_perusahaan = pp.id_pembimbing
    ORDER BY pr.id_perusahaan
    LIMIT 10");

    echo "\nDetailed Perusahaan Data:\n";
    echo str_repeat("=", 80) . "\n";
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "ID Perusahaan: " . $row['id_perusahaan'] . "\n";
        echo "Nama Perusahaan: " . ($row['nama_perusahaan'] ?? 'NULL') . "\n";
        echo "NIP Pembimbing Sekolah: " . ($row['nip_pembimbing_sekolah'] ?? 'NULL') . "\n";
        echo "ID Pembimbing Perusahaan: " . ($row['id_pembimbing_perusahaan'] ?? 'NULL') . "\n";
        echo "Pembimbing Sekolah: " . ($row['nama_pembimbing_sekolah'] ?? 'NULL') . "\n";
        echo "Pembimbing Perusahaan: " . ($row['nama_pembimbing_perusahaan'] ?? 'NULL') . "\n";
        echo str_repeat("-", 50) . "\n";
    }

} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage() . "\n";
}