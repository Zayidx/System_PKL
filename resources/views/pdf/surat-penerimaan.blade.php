<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .content { margin: 20px 0; }
        .footer { margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h3>Surat Penerimaan Pemagangan</h3>
        <p>Nomor: {{ $pengajuan->id_pengajuan }}/PKL/{{ date('Y') }}</p>
    </div>
    <div class="content">
        <p>Kepada Yth.<br>
        {{ $pengajuan->siswa->nama_siswa }}<br>
        di Tempat</p>
        <p>Dengan hormat,</p>
        <p>Dengan ini kami menyatakan bahwa pengajuan pemagangan Anda di {{ $pengajuan->perusahaan->nama_perusahaan }} telah <b>DITERIMA</b>.</p>
        <p>Silakan menghubungi perusahaan untuk proses selanjutnya.</p>
        <p>Terima kasih.</p>
        <table style="width:100%; margin-bottom: 10px;">
            <tr><td>Tanggal PKL</td><td>: {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai)->format('d M Y') }}</td></tr>
            <tr><td>Link CV</td><td>: <a href="{{ $pengajuan->link_cv }}">{{ $pengajuan->link_cv }}</a></td></tr>
        </table>
    </div>
    <div class="footer">
        <p>Hormat kami,<br>Admin Sekolah</p>
    </div>
</body>
</html> 