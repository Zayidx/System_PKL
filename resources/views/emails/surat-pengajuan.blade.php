<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .btn { display: inline-block; padding: 8px 16px; margin: 8px 4px; border-radius: 4px; text-decoration: none; color: #fff; }
        .btn-approve { background: #28a745; }
        .btn-decline { background: #dc3545; }
    </style>
</head>
<body>
    <h3>Pengajuan Pemagangan Siswa</h3>
    <p>Yth. {{ $pengajuan->perusahaan->nama_perusahaan }}<br>di Tempat</p>
    <p>Berikut detail pengajuan pemagangan:</p>
    <ul>
        <li><b>Nama Siswa:</b> {{ $pengajuan->siswa->nama_siswa }}</li>
        <li><b>NIS:</b> {{ $pengajuan->siswa->nis }}</li>
        <li><b>Kelas:</b> {{ $pengajuan->siswa->kelas->nama_kelas ?? '-' }}</li>
        <li><b>Jurusan:</b> {{ $pengajuan->siswa->jurusan->nama_jurusan_lengkap ?? '-' }}</li>
    </ul>
    <p>Silakan klik tombol berikut untuk memberikan keputusan:</p>
    <a href="{{ $approveUrl }}" class="btn btn-approve">Setujui Pengajuan</a>
    <a href="{{ $declineUrl }}" class="btn btn-decline">Tolak Pengajuan</a>
    <p>Terima kasih atas perhatian dan kerjasamanya.</p>
</body>
</html> 