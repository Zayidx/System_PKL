<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Siswa Telah Diterima di Perusahaan Lain</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background-color: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .alert { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .info-box { background-color: #e7f3ff; border: 1px solid #b3d9ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Notifikasi Pengajuan Prakerin</h2>
        </div>
        
        <div class="content">
            <div class="alert">
                <strong>Perhatian!</strong> Siswa yang Anda terima telah diterima di perusahaan lain.
            </div>
            
            <div class="info-box">
                <h3>Informasi Siswa:</h3>
                <p><strong>Nama:</strong> {{ $siswa->nama_siswa }}</p>
                <p><strong>NIS:</strong> {{ $siswa->nis }}</p>
                <p><strong>Jurusan:</strong> {{ $siswa->jurusan->nama_jurusan_singkat ?? 'N/A' }}</p>
            </div>
            
            <div class="info-box">
                <h3>Perusahaan yang Menerima:</h3>
                <p><strong>Nama Perusahaan:</strong> {{ $perusahaanDiterima->nama_perusahaan }}</p>
                <p><strong>Alamat:</strong> {{ $perusahaanDiterima->alamat_perusahaan }}</p>
                <p><strong>Email:</strong> {{ $perusahaanDiterima->email_perusahaan }}</p>
            </div>
            
            <p>Dengan ini, pengajuan prakerin siswa tersebut di perusahaan Anda telah dibatalkan secara otomatis.</p>
            
            <p>Terima kasih atas kerjasamanya.</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem PKL</p>
            <p>© {{ date('Y') }} Sistem PKL</p>
        </div>
    </div>
</body>
</html> 