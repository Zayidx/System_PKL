<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Token Expired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .error-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="error-container">
                    <div class="header">
                        <div class="error-icon">
                            <i class="bi bi-clock-exclamation"></i>
                        </div>
                        <h1>Token Expired</h1>
                        <p class="mb-0">Link penilaian sudah tidak berlaku</p>
                    </div>
                    
                    <div class="content">
                        <div class="alert alert-warning">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Link Sudah Expired</h5>
                            <p class="mb-0">Link penilaian yang Anda akses sudah tidak berlaku karena melebihi batas waktu 7 hari.</p>
                        </div>
                        
                        <p>Untuk melanjutkan penilaian:</p>
                        <ul class="text-start">
                            <li>Hubungi admin sistem untuk mendapatkan link baru</li>
                            <li>Atau tunggu email penilaian yang baru</li>
                        </ul>
                        
                        <div class="mt-4">
                            <p class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Link penilaian berlaku selama 7 hari sejak email dikirim.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 