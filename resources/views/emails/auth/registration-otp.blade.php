<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Pendaftaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #435ebe; /* Menggunakan warna primer dari tema Mazer Anda */
            margin: 0;
            font-size: 24px;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            padding: 20px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin: 30px 0;
            color: #000;
        }
        .content p {
            margin: 0 0 15px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #777777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'InfoPKL') }}</h1>
            <h2>Verifikasi Pendaftaran Akun Anda</h2>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Gunakan kode berikut untuk menyelesaikan proses pendaftaran Anda. Jangan berikan kode ini kepada siapapun.</p>
            
            {{-- Variabel $otpCode diambil dari Mailable (RegistrationOtpMail.php) --}}
            <div class="otp-code">{{ $otpCode }}</div>
            
            <p>Kode ini hanya berlaku selama 5 menit.</p>
            <p>Jika Anda tidak merasa mendaftar, harap abaikan email ini.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'InfoPKL') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
