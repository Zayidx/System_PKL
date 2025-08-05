<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Penilaian Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .success-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .content {
            padding: 40px;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            font-weight: bold;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="success-container">
                    <div class="header">
                        <div class="success-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h1>Penilaian Berhasil Disimpan!</h1>
                        <p class="mb-0">Terima kasih telah memberikan penilaian untuk siswa kami</p>
                    </div>
                    
                    <div class="content text-center">
                        <div class="alert alert-success">
                            <h5><i class="bi bi-info-circle me-2"></i>Penilaian Telah Diproses</h5>
                            <p class="mb-0">Data penilaian telah berhasil disimpan ke dalam sistem dan akan dapat diakses oleh siswa dan staff sekolah.</p>
                        </div>
                        
                        <!-- Informasi Penilaian -->
                        @if($prakerin && $penilaian)
                            <div class="info-card text-start">
                                <h5><i class="bi bi-person-check me-2"></i>Detail Penilaian</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nama Siswa:</strong><br>{{ $prakerin->siswa->nama_siswa ?? 'N/A' }}</p>
                                        <p><strong>NIS:</strong><br>{{ $prakerin->siswa->nis ?? 'N/A' }}</p>
                                        <p><strong>Perusahaan:</strong><br>{{ $prakerin->perusahaan->nama_perusahaan ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>ID Penilaian:</strong><br>{{ $penilaian->id_penilaian ?? 'N/A' }}</p>
                                        <p><strong>Tanggal Penilaian:</strong><br>{{ now()->format('d M Y H:i') }}</p>
                                        <p><strong>Status:</strong><br><span class="badge bg-success">Selesai</span></p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Info:</strong> Penilaian berhasil disimpan ke dalam sistem.
                            </div>
                        @endif
                        
                        <!-- Ringkasan Nilai -->
                        @if($penilaian && $penilaian->kompetensi && $penilaian->kompetensi->count() > 0)
                            @php
                                // Pastikan data kompetensi dan nilai tersedia
                                $kompetensiWithNilai = $penilaian->kompetensi()->withPivot('nilai')->get();
                                $nilaiRataRata = $kompetensiWithNilai->avg('pivot.nilai');
                                $nilaiTertinggi = $kompetensiWithNilai->max('pivot.nilai');
                                $nilaiTerendah = $kompetensiWithNilai->min('pivot.nilai');
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
                                            @foreach($kompetensiWithNilai as $index => $kompetensi)
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
                        @endif
                        
                        <div class="mt-4">
                            @if($penilaian && $penilaian->kompetensi && $penilaian->kompetensi->count() > 0)
                                <a href="#" class="btn-home me-2" onclick="window.print()">
                                    <i class="bi bi-printer me-2"></i>Cetak Penilaian
                                </a>
                            @endif
                            <a href="/" class="btn btn-secondary">
                                <i class="bi bi-house me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Penilaian ini akan otomatis tersedia di sistem untuk siswa dan staff sekolah
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
            // Bersihkan localStorage untuk token ini
            const urlParams = new URLSearchParams(window.location.search);
            const token = window.location.pathname.split('/').pop();
            if (token) {
                localStorage.removeItem('formSubmitted_' + token);
            }
            
            // Tampilkan SweetAlert sukses jika ada
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                });
            @endif
            
            // Tampilkan SweetAlert sukses jika showSuccessAlert = true
            @if(isset($showSuccessAlert) && $showSuccessAlert)
                Swal.fire({
                    icon: 'success',
                    title: 'Penilaian Berhasil Disimpan!',
                    text: 'Terima kasih telah memberikan penilaian untuk siswa ini.',
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
</body>
</html> 