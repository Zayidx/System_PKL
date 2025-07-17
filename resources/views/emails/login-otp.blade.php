<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Login</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #0d6efd; }
        .otp-code { font-size: 36px; font-weight: bold; text-align: center; letter-spacing: 5px; padding: 20px; background-color: #e9ecef; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>InfoPKL</h1>
            <h2>Verifikasi Login Anda</h2>
        </div>
        <p>Halo,</p>
        <p>Gunakan kode berikut untuk menyelesaikan proses login Anda. Jangan berikan kode ini kepada siapapun.</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>Kode ini hanya berlaku selama 5 menit.</p>
        <p>Jika Anda tidak merasa meminta kode ini, harap abaikan email ini.</p>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} InfoPKL. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
