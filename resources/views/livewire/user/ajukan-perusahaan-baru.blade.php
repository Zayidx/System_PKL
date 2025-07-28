{{-- Ganti kode view Anda dengan kode di bawah ini --}}
{{-- Lokasi file: resources/views/livewire/user/pengajuan-form.blade.php (contoh) --}}


<div class="container-fluid py-4">

    <div class="card shadow-lg mx-auto" style="max-width: 700px; border: none; border-radius: 1rem;">
        
        <div class="card-header bg-primary text-white" style="border-radius: 1rem 1rem 0 0;">
            <h4 class="card-title text-white mb-0"><i class="bi bi-building-add me-2"></i>Form Pengajuan Perusahaan Baru</h4>
        </div>
        <div class="card-body p-4 p-md-5">
            <p class="card-text text-muted mb-4">
                Silakan isi formulir di bawah ini untuk mengusulkan perusahaan baru. Pastikan data yang Anda masukkan sudah benar dan valid.
            </p>

            {{-- Menampilkan pesan sukses/error dengan gaya dari template --}}
            @if(session('success'))
                <div class="alert alert-light-success color-success border-0 fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-light-danger color-danger border-0 fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="submit" novalidate>
                {{-- Nama Perusahaan --}}
                <div class="form-group mb-4">
                    <label for="nama_mitra" class="form-label fw-bold">Nama Perusahaan <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" id="nama_mitra" class="form-control form-control-xl @error('nama_mitra') is-invalid @enderror" wire:model.defer="nama_mitra" placeholder="Contoh: PT. Inovasi Digital Nusantara" required>
                        <div class="form-control-icon" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); font-size: 1.2rem;">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                    @error('nama_mitra') <div class="invalid-feedback d-block mt-1">{{ $message }}</div> @enderror
                </div>
                
                {{-- Alamat Perusahaan --}}
                <div class="form-group mb-4">
                    <label for="alamat_mitra" class="form-label fw-bold">Alamat Lengkap Perusahaan <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" id="alamat_mitra" class="form-control form-control-xl @error('alamat_mitra') is-invalid @enderror" wire:model.defer="alamat_mitra" placeholder="Contoh: Jl. Jenderal Sudirman Kav. 52-53, Jakarta Selatan" required>
                        <div class="form-control-icon" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); font-size: 1.2rem;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                    </div>
                    @error('alamat_mitra') <div class="invalid-feedback d-block mt-1">{{ $message }}</div> @enderror
                </div>
                
                {{-- Kontak dan Email dalam satu baris --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="email_mitra" class="form-label fw-bold">Email Perusahaan <span class="text-muted">(Opsional)</span></label>
                            <div class="position-relative">
                                <input type="email" id="email_mitra" class="form-control form-control-xl @error('email_mitra') is-invalid @enderror" wire:model.defer="email_mitra" placeholder="kontak@perusahaan.com">
                                <div class="form-control-icon" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); font-size: 1.2rem;">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>
                            @error('email_mitra') <div class="invalid-feedback d-block mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label for="kontak_mitra" class="form-label fw-bold">Kontak Perusahaan <span class="text-muted">(Opsional)</span></label>
                            <div class="position-relative">
                                <input type="text" id="kontak_mitra" class="form-control form-control-xl @error('kontak_mitra') is-invalid @enderror" wire:model.defer="kontak_mitra" placeholder="081234567890">
                                <div class="form-control-icon" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); font-size: 1.2rem;">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                            </div>
                            @error('kontak_mitra') <div class="invalid-feedback d-block mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('user.pengajuan') }}" class="btn btn-danger" wire:navigate>
                        <i class="bi bi-arrow-left-circle me-2"></i>Kembali
                    </a>
                    
                    <button type="submit" class="btn btn-primary " wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submit">
                            <i class="bi bi-check-circle-fill me-2"></i>Ajukan Sekarang
                        </span>
                        <span wire:loading wire:target="submit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
