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
        <h3>Surat Pengajuan Pemagangan</h3>
        <p>Nomor: {{ $pengajuan->id_pengajuan }}/PKL/{{ date('Y') }}</p>
    </div>
    <div class="content">
        <p>Kepada Yth.<br>
        {{ $pengajuan->perusahaan->nama_perusahaan }}<br>
        di Tempat</p>
        <p>Dengan hormat,</p>
        <p>Bersama surat ini, kami mengajukan permohonan pemagangan untuk siswa berikut:</p>
        <table style="width:100%; margin-bottom: 10px;">
            <tr><td>Nama Siswa</td><td>: {{ $pengajuan->siswa->nama_siswa }}</td></tr>
            <tr><td>NIS</td><td>: {{ $pengajuan->siswa->nis }}</td></tr>
            <tr><td>Jurusan</td><td>: {{ $pengajuan->siswa->jurusan->nama_jurusan_lengkap ?? '-' }}</td></tr>
        </table>
        <p>Mohon kesediaan Bapak/Ibu untuk menerima siswa tersebut sebagai peserta magang di perusahaan yang Bapak/Ibu pimpin.</p>
        <p>Atas perhatian dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.</p>
    </div>
    <div class="footer">
        <p>Hormat kami,<br>Admin Sekolah</p>
    </div>
</body>
</html> 