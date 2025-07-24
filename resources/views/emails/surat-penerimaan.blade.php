<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
    </style>
</head>
<body>
    <h3>Pengajuan Magang Diterima</h3>
    <p>Yth. {{ $pengajuan->siswa->nama_siswa }}<br>di Tempat</p>
    <p>Selamat! Pengajuan pemagangan Anda di <b>{{ $pengajuan->perusahaan->nama_perusahaan }}</b> <b>DITERIMA</b>.</p>
    <p>Silakan menghubungi perusahaan untuk proses selanjutnya.</p>
    <p>Terima kasih.</p>
    <li><b>Tanggal PKL:</b> {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}</li>
    <li><b>Link CV:</b> <a href="{{ $pengajuan->link_cv }}">{{ $pengajuan->link_cv }}</a></li>
</body>
</html> 