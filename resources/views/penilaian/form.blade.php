<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Form Penilaian PKL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        .nilai-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
        }
        .nilai-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .kompetensi-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .kompetensi-item:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            font-weight: bold;
            border-radius: 8px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <div class="header">
                        <h1><i class="bi bi-star-fill me-2"></i>Form Penilaian PKL</h1>
                        <p class="mb-0">Sistem Informasi Prakerin</p>
                    </div>
                    
                    <div class="content">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <!-- Informasi Prakerin -->
                        <div class="info-card">
                            <h4><i class="bi bi-info-circle me-2"></i>Informasi Prakerin</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama Siswa:</strong><br>{{ $prakerin->siswa->nama_siswa }}</p>
                                    <p><strong>NIS:</strong><br>{{ $prakerin->siswa->nis }}</p>
                                    <p><strong>Kelas:</strong><br>{{ $prakerin->siswa->kelas->nama_kelas ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Jurusan:</strong><br>{{ $prakerin->siswa->jurusan->nama_jurusan_singkat ?? 'N/A' }}</p>
                                    <p><strong>Perusahaan:</strong><br>{{ $prakerin->perusahaan->nama_perusahaan }}</p>
                                    <p><strong>Periode:</strong><br>
                                        {{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Penilaian -->
                        <form action="{{ url('/penilaian/submit/' . $token) }}" method="POST" id="penilaianForm">
                            @csrf
                            <input type="hidden" name="submitted" value="0" id="formSubmitted">
                            
                            <h4 class="mb-4"><i class="bi bi-list-check me-2"></i>Penilaian Kompetensi</h4>
                            
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-2"></i>Panduan Penilaian:</h6>
                                <ul class="mb-0">
                                    <li><strong>85-100:</strong> Sangat Baik</li>
                                    <li><strong>75-84:</strong> Baik</li>
                                    <li><strong>60-74:</strong> Cukup</li>
                                    <li><strong>0-59:</strong> Kurang</li>
                                </ul>
                            </div>
                            
                            @foreach($kompetensi as $index => $komp)
                                <div class="kompetensi-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1">{{ $index + 1 }}. {{ $komp->nama_kompetensi }}</h6>
                                            <small class="text-muted">Berikan nilai berdasarkan kinerja siswa dalam kompetensi ini</small>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" 
                                                   name="nilai[{{ $komp->id_kompetensi }}]" 
                                                   class="form-control nilai-input @error('nilai.' . $komp->id_kompetensi) is-invalid @enderror"
                                                   min="0" 
                                                   max="100" 
                                                   placeholder="0-100"
                                                   required>
                                            @error('nilai.' . $komp->id_kompetensi)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Komentar -->
                            <div class="mb-4">
                                <label for="komentar" class="form-label">
                                    <i class="bi bi-chat-text me-2"></i>Komentar (Opsional)
                                </label>
                                <textarea class="form-control @error('komentar') is-invalid @enderror" 
                                          id="komentar" 
                                          name="komentar" 
                                          rows="4" 
                                          placeholder="Berikan komentar atau saran untuk siswa..."></textarea>
                                @error('komentar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Tombol Submit -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="bi bi-check-circle me-2"></i>Submit Penilaian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Mencegah akses melalui browser back button
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.history.pushState(null, null, window.location.href);
        };
        
        // Mencegah form dikirim berulang
        let formSubmitted = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah form sudah disubmit sebelumnya
            const formSubmitted = localStorage.getItem('formSubmitted_' + '{{ $token }}');
            if (formSubmitted === 'true') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Sudah Disubmit',
                    text: 'Form penilaian sudah disubmit sebelumnya. Silakan refresh halaman jika ingin mengirim ulang.',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '/';
                });
                return;
            }
            
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-submit');
            
            form.addEventListener('submit', function(e) {
                if (formSubmitted) {
                    e.preventDefault();
                    return false;
                }
                
                // Validasi form
                const nilaiInputs = document.querySelectorAll('input[name^="nilai"]');
                let isValid = true;
                
                nilaiInputs.forEach(input => {
                    if (!input.value || input.value < 0 || input.value > 100) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua nilai kompetensi (0-100)',
                        confirmButtonColor: '#dc3545'
                    });
                    return false;
                }
                
                // Tampilkan loading
                Swal.fire({
                    title: 'Mengirim Penilaian...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Set flag bahwa form sudah disubmit
                formSubmitted = true;
                document.getElementById('formSubmitted').value = '1';
                localStorage.setItem('formSubmitted_' + '{{ $token }}', 'true');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Mengirim...';
            });
            
            // Tampilkan SweetAlert untuk error jika ada
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc3545'
                });
            @endif
        });
    </script>
</body>
</html> 