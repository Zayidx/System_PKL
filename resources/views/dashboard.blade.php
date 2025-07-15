@extends('components.layouts.layout-admin-dashboard')
@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-4">
    <h2 class="fw-bold mb-3">Dashboard Pemagangan</h2>
    <p class="text-muted mb-4">Berikut adalah ringkasan aktivitas Anda.</p>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-3">
                <h5 class="mb-1">2</h5>
                <p class="text-muted">Pemagangan Aktif</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-3">
                <h5 class="mb-1">5</h5>
                <p class="text-muted">Menunggu Respon</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-3">
                <h5 class="mb-1">8</h5>
                <p class="text-muted">Selesai</p>
            </div>
        </div>
    </div>

    <h4 class="mb-3">Pemagangan Tersedia</h4>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">Frontend Developer Inovatik</h5>
            <p class="mb-1 text-muted">PT. Tech Indonesia</p>

            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge bg-secondary">Jakarta</span>
                <span class="badge bg-secondary">3 bulan</span>
                <span class="badge bg-secondary">45 pelamar</span>
                <span class="badge bg-info text-dark">React</span>
                <span class="badge bg-info text-dark">TypeScript</span>
                <span class="badge bg-info text-dark">Next.js</span>
                <span class="badge bg-info text-dark">Remote</span>
            </div>

            <p class="text-muted">Deadline: 20 Des 2025</p>

            <a href="#" class="btn btn-primary">Lamar Sekarang</a>
        </div>
    </div>
</div>
@endsection
