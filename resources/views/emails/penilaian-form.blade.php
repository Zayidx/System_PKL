<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penilaian PKL</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .scale-info {
            background: #e8f4fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Form Penilaian PKL</h1>
        <p>Sistem Penilaian Praktik Kerja Lapangan</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $pembimbingPerusahaan->nama ?? 'Pembimbing Perusahaan' }}</strong>,</p>

        <p>Terima kasih telah memberikan kesempatan kepada siswa kami untuk melakukan Praktik Kerja Lapangan (PKL) di perusahaan Anda.</p>

        <div class="info-box">
            <h3>📊 Informasi Prakerin</h3>
            <p><strong>Nama Siswa:</strong> {{ $siswa->nama_siswa }}</p>
            <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
            <p><strong>Perusahaan:</strong> {{ $perusahaan->nama_perusahaan }}</p>
            <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d F Y') }}</p>
            <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d F Y') }}</p>
        </div>

        <p>Sekarang kami memohon bantuan Anda untuk memberikan penilaian terhadap kinerja siswa selama PKL. Penilaian ini sangat penting untuk evaluasi dan pengembangan kompetensi siswa.</p>

        <div class="scale-info">
            <h4>📈 Skala Penilaian</h4>
            <p><strong>1 = Sangat Kurang</strong> | <strong>2 = Kurang</strong> | <strong>3 = Cukup</strong> | <strong>4 = Baik</strong> | <strong>5 = Sangat Baik</strong></p>
        </div>

        <div style="text-align: center;">
            <a href="http://192.168.18.94:8000/penilaian/form/{{ $token }}" class="btn">
                📝 Isi Form Penilaian
            </a>
        </div>

        <div class="info-box">
            <h4>⚠️ Penting</h4>
            <ul>
                <li>Link ini hanya berlaku selama <strong>7 hari</strong></li>
                <li>Form penilaian hanya dapat diisi <strong>satu kali</strong></li>
                <li>Pastikan semua kompetensi dinilai dengan objektif</li>
                <li>Penilaian akan digunakan untuk evaluasi akademik siswa</li>
            </ul>
        </div>

        <p>Terima kasih atas kerjasama dan dukungan Anda dalam pengembangan kompetensi siswa kami.</p>

        <p>Salam,<br>
        <strong>Tim PKL Sekolah</strong></p>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem PKL</p>
        <p>Jika ada pertanyaan, silakan hubungi tim PKL sekolah</p>
    </div>
</body>
</html>