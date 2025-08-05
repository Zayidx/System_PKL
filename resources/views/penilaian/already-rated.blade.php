<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Penilaian Sudah Dilakukan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .info-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .content {
            padding: 40px;
            text-align: center;
        }
        .info-icon {
            font-size: 4rem;
            color: #17a2b8;
            margin-bottom: 20px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="info-container">
                    <div class="header">
                        <div class="info-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h1>Penilaian Sudah Dilakukan</h1>
                        <p class="mb-0">Terima kasih telah memberikan penilaian</p>
                    </div>
                    
                    <div class="content">
                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle me-2"></i>Penilaian Telah Ada</h5>
                            <p class="mb-0">
                                @if(isset($message))
                                    {{ $message }}
                                @else
                                    Anda sudah memberikan penilaian untuk siswa ini sebelumnya. Form penilaian tidak dapat dikirim lagi untuk mencegah duplikasi data.
                                @endif
                            </p>
                        </div>
                        
                        <!-- Informasi Prakerin -->
                        @if($prakerin && $penilaian)
                            <div class="info-card text-start">
                                <h5><i class="bi bi-person-check me-2"></i>Informasi Prakerin</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nama Siswa:</strong><br>{{ $prakerin->siswa->nama_siswa ?? 'N/A' }}</p>
                                        <p><strong>NIS:</strong><br>{{ $prakerin->siswa->nis ?? 'N/A' }}</p>
                                        <p><strong>Perusahaan:</strong><br>{{ $prakerin->perusahaan->nama_perusahaan ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>ID Penilaian:</strong><br>{{ $penilaian->id_penilaian ?? 'N/A' }}</p>
                                        <p><strong>Periode PKL:</strong><br>
                                            @if($prakerin->tanggal_mulai && $prakerin->tanggal_selesai)
                                                {{ \Carbon\Carbon::parse($prakerin->tanggal_mulai)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($prakerin->tanggal_selesai)->format('d M Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                        <p><strong>Status:</strong><br><span class="badge bg-success">Sudah Dinilai</span></p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Info:</strong> Penilaian sudah ada dalam sistem.
                            </div>
                        @endif
                        
                        <!-- Ringkasan Nilai -->
                        @if($penilaian && $penilaian->kompetensi && $penilaian->kompetensi->count() > 0)
                            @php
                                $nilaiRataRata = $penilaian->kompetensi->avg('pivot.nilai');
                                $nilaiTertinggi = $penilaian->kompetensi->max('pivot.nilai');
                                $nilaiTerendah = $penilaian->kompetensi->min('pivot.nilai');
                            @endphp
                        
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ number_format($nilaiRataRata, 1) }}</h3>
                                            <small>Rata-rata</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $nilaiTertinggi }}</h3>
                                            <small>Tertinggi</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $nilaiTerendah }}</h3>
                                            <small>Terendah</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Peringatan:</strong> Data penilaian tidak tersedia atau belum lengkap.
                            </div>
                        @endif
                        
                        <!-- Detail Nilai -->
                        @if($penilaian && $penilaian->kompetensi && $penilaian->kompetensi->count() > 0)
                            <div class="text-start">
                                <h5><i class="bi bi-list-check me-2"></i>Detail Nilai Kompetensi</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Kompetensi</th>
                                                <th class="text-center">Nilai</th>
                                                <th class="text-center">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($penilaian->kompetensi as $index => $kompetensi)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $kompetensi->nama_kompetensi }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $kompetensi->pivot->nilai >= 85 ? 'success' : ($kompetensi->pivot->nilai >= 75 ? 'warning' : 'danger') }}">
                                                            {{ $kompetensi->pivot->nilai }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($kompetensi->pivot->nilai >= 85)
                                                            <span class="text-success">Sangat Baik</span>
                                                        @elseif($kompetensi->pivot->nilai >= 75)
                                                            <span class="text-warning">Baik</span>
                                                        @else
                                                            <span class="text-danger">Perlu Perbaikan</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Peringatan:</strong> Data penilaian tidak tersedia atau belum lengkap.
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            @if($penilaian && $penilaian->kompetensi && $penilaian->kompetensi->count() > 0)
                                <button class="btn btn-primary me-2" onclick="window.print()">
                                    <i class="bi bi-printer me-2"></i>Cetak Penilaian
                                </button>
                            @endif
                            <a href="/" class="btn btn-secondary">
                                <i class="bi bi-house me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Penilaian ini sudah tersedia di sistem untuk siswa dan staff sekolah
                            </small>
                        </div>
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
        
        document.addEventListener('DOMContentLoaded', function() {
            // Bersihkan localStorage untuk token ini jika ada
            const token = window.location.pathname.split('/').pop();
            if (token) {
                localStorage.removeItem('formSubmitted_' + token);
            }
            
            // Tampilkan SweetAlert untuk memberitahu bahwa penilaian sudah ada
            Swal.fire({
                icon: 'info',
                title: 'Penilaian Sudah Ada',
                text: 'Anda sudah memberikan penilaian untuk siswa ini sebelumnya. Form penilaian tidak dapat dikirim lagi.',
                confirmButtonColor: '#17a2b8',
                confirmButtonText: 'OK'
            });
        });
    </script>
</body>
</html> 