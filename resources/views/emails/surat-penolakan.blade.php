<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
    </style>
</head>
<body>
    <h3>Pengajuan Magang Ditolak</h3>
    <p>Yth. {{ $pengajuan->siswa->nama_siswa }}<br>di Tempat</p>
    <p>Mohon maaf, pengajuan pemagangan Anda di <b>{{ $pengajuan->perusahaan->nama_perusahaan }}</b> <b>DITOLAK</b>.</p>
    <p>Terima kasih atas partisipasi Anda.</p>
    <li><b>Tanggal PKL:</b> {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}</li>
    <li><b>Link CV:</b> <a href="{{ $pengajuan->link_cv }}">{{ $pengajuan->link_cv }}</a></li>
</body>
</html> 