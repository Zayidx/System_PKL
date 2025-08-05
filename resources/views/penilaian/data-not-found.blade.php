<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tidak Ditemukan</title>
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
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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
            color: #6c757d;
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
                            <i class="bi bi-search"></i>
                        </div>
                        <h1>Data Tidak Ditemukan</h1>
                        <p class="mb-0">Informasi prakerin tidak dapat ditemukan</p>
                    </div>
                    
                    <div class="content">
                        <div class="alert alert-secondary">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Data Prakerin Tidak Ditemukan</h5>
                            <p class="mb-0">Data prakerin yang terkait dengan token ini tidak dapat ditemukan dalam sistem.</p>
                        </div>
                        
                        <p>Kemungkinan penyebab:</p>
                        <ul class="text-start">
                            <li>Data prakerin telah dihapus dari sistem</li>
                            <li>Token tidak sesuai dengan data yang ada</li>
                            <li>Terjadi kesalahan dalam sistem</li>
                        </ul>
                        
                        <div class="mt-4">
                            <p class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Jika Anda merasa ini adalah kesalahan, silakan hubungi admin sistem.
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