<div id="auth-left">
    <div class="auth-logo">
        <a href="#"><img src="data:image/svg+xml,..." alt="Logo"></a>
    </div>

    @if (!$showOtpForm)
        {{-- TAMPILAN FORM REGISTRASI AWAL --}}
        <h1 class="auth-title">Sign Up</h1>
        <p class="auth-subtitle mb-5">Isi data Anda untuk mendaftar.</p>

        <form wire:submit="submitRegistrationDetails">
            {{-- Menampilkan error umum (misal: gagal kirim OTP) --}}
            @error('email')
                @if($message == 'Gagal mengirim OTP. Pastikan konfigurasi email benar dan coba lagi.')
                    <div class="alert alert-danger">{{ $message }}</div>
                @endif
            @enderror

            <div class="form-group position-relative has-icon-left mb-4">
                <input type="email" wire:model.live.debounce.300ms="email" autofocus class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email">
                <div class="form-control-icon"><i class="bi bi-envelope"></i></div>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group position-relative has-icon-left mb-4">
                <input type="text" wire:model.live.debounce.300ms="username" class="form-control form-control-xl @error('username') is-invalid @enderror" placeholder="Nama Lengkap">
                <div class="form-control-icon"><i class="bi bi-person"></i></div>
                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group position-relative has-icon-left mb-4">
                <input type="password" wire:model.live.debounce.300ms="password" class="form-control form-control-xl @error('password') is-invalid @enderror" placeholder="Password">
                <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submitRegistrationDetails">Daftar & Kirim OTP</span>
                <span wire:loading wire:target="submitRegistrationDetails" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </form>
    @else
        {{-- TAMPILAN FORM VERIFIKASI OTP --}}
        <h1 class="auth-title">Verifikasi Email Anda</h1>
        <p class="auth-subtitle mb-5">Kami telah mengirim kode OTP ke <strong>{{ $email }}</strong>. Silakan periksa kotak masuk Anda.</p>

        <form wire:submit="verifyOtpAndCreateUser">
            @error('otp') <div class="alert alert-danger">{{ $message }}</div> @enderror

            <div class="form-group position-relative has-icon-left mb-4">
                <input type="text" wire:model.live="otp" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="form-control form-control-xl @error('otp') is-invalid @enderror" placeholder="Masukkan 6 digit OTP">
                <div class="form-control-icon"><i class="bi bi-key"></i></div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="verifyOtpAndCreateUser">Verifikasi & Buat Akun</span>
                <span wire:loading wire:target="verifyOtpAndCreateUser" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
        </form>

        <div class="text-center mt-5">
            <p class="text-gray-600">Salah email? <a href="#" wire:click.prevent="cancelOtp" class="font-bold">Kembali</a></p>
        </div>
    @endif

    <div class="text-center mt-5 text-lg fs-4">
        <p class='text-gray-600'>Sudah punya akun? <a wire:navigate href="{{ route('login') }}" class="font-bold">Log in</a>.</p>
    </div>
</div>
