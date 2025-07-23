<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 16px; text-align: center; padding: 40px; }
        .status-success { color: #28a745; font-size: 2rem; }
        .status-fail { color: #dc3545; font-size: 2rem; }
        .info { margin-top: 20px; font-size: 1.1rem; }
    </style>
</head>
<body>
    @if($status === 'diterima')
        <div class="status-success">Pengajuan DITERIMA</div>
        <div class="info">Siswa <b>{{ $pengajuan->siswa->nama_siswa }}</b> resmi diterima magang di <b>{{ $pengajuan->perusahaan->nama_perusahaan }}</b>.<br>Surat penerimaan telah dikirim ke email siswa.</div>
    @else
        <div class="status-fail">Pengajuan DITOLAK</div>
        <div class="info">Siswa <b>{{ $pengajuan->siswa->nama_siswa }}</b> <b>tidak diterima</b> magang di <b>{{ $pengajuan->perusahaan->nama_perusahaan }}</b>.<br>Surat penolakan telah dikirim ke email siswa.</div>
    @endif
</body>
</html> 