{{-- Proses lupa kata sandi berbasis OTP hingga reset password. --}}
<div id="auth-left">
    <a href="#" class="mb-4 logo">
        <i class="fas fa-graduation-cap"></i> InfoPKL
    </a>
    @if (!session('success'))
        @if (!$showOtpForm && !$showResetForm)
            <h1 class="mt-5 auth-title">Lupa Password</h1>
            <p class="mb-5 auth-subtitle pe-5">Masukkan email yang terdaftar untuk reset password.</p>
            <form wire:submit='submitEmail' novalidate>
                @if ($errors->has('credentials'))
                    <div class="mb-3 alert alert-danger">{{ $errors->first('credentials') }}</div>
                @endif
                <div class="mb-4 form-group position-relative has-icon-left">
                    <input required type="email" wire:model.blur='email' class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email">
                    <div class="form-control-icon"><i class="bi bi-envelope"></i></div>
                    @error('email')<div class="invalid-feedback"><i class="bx bx-radio-circle"></i> {{ $message }}</div>@enderror
                </div>
                <button type="submit" class="mt-3 shadow-lg btn btn-primary btn-block btn-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target='submitEmail'>Kirim OTP</span>
                    <span wire:loading wire:target='submitEmail' class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                <p class="m-0 mt-5 text-xl text-center text-dark">
                    <a href="{{ route('masuk') }}" class="text-primary fw-bold text-decoration-underline" wire:navigate>Kembali ke Login</a>
                </p>
            </form>
        @elseif($showOtpForm && !$showResetForm)
            <h1 class="mt-5 auth-title">Verifikasi Email</h1>
            <p class="mb-5 auth-subtitle pe-5">Kode OTP telah dikirim ke <strong>{{ $email }}</strong>. Masukkan kode OTP untuk reset password.</p>
            <div x-data="{ countdown: 60, timerRunning: true }"
                 x-init="
                    timer = setInterval(() => {
                        if (countdown > 0) { countdown--; } else { clearInterval(timer); timerRunning = false; }
                    }, 1000);
                 "
                 @otp-sent.window="
                    countdown = 60;
                    timerRunning = true;
                    clearInterval(timer);
                    timer = setInterval(() => {
                        if (countdown > 0) { countdown--; } else { clearInterval(timer); timerRunning = false; }
                    }, 1000);
                 ">
                <form wire:submit='verifyOtp'>
                    @if ($errors->has('otp'))
                        <div class="mb-3 alert alert-danger">{{ $errors->first('otp') }}</div>
                    @endif
                    <div class="mb-4 form-group position-relative has-icon-left">
                        <input required type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" wire:model.blur='otp' class="form-control form-control-xl @error('otp') is-invalid @enderror" placeholder="Masukkan 6 digit OTP">
                        <div class="form-control-icon"><i class="bi bi-key"></i></div>
                        @error('otp')<div class="invalid-feedback"><i class="bx bx-radio-circle"></i> {{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="mt-3 shadow-lg btn btn-primary btn-block btn-lg" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target='verifyOtp'>Verifikasi OTP</span>
                        <span wire:loading wire:target='verifyOtp' class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                </form>
                <div class="mt-4 text-center">
                    <template x-if="timerRunning">
                        <p class="text-muted">Kirim ulang kode dalam <span x-text="countdown" class="fw-bold"></span> detik</p>
                    </template>
                    <template x-if="!timerRunning">
                        <p class="text-muted">Tidak menerima kode? <a href="#" wire:click.prevent="resendOtp" class="font-bold">Kirim Ulang</a></p>
                    </template>
                    <a href="#" wire:click.prevent="cancelOtp" class="text-muted d-block mt-2">Kembali</a>
                </div>
            </div>
        @elseif($showResetForm)
            <h1 class="mt-5 auth-title">Reset Password Baru</h1>
            <p class="mb-5 auth-subtitle pe-5">Masukkan password baru untuk akun <strong>{{ $email }}</strong>.</p>
            <form wire:submit='resetPassword' novalidate>
                <div class="mb-4 form-group position-relative has-icon-left">
                    <input required type="password" wire:model.blur='password' class="form-control form-control-xl @error('password') is-invalid @enderror" placeholder="Password Baru">
                    <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                    @error('password')<div class="invalid-feedback"><i class="bx bx-radio-circle"></i> {{ $message }}</div>@enderror
                </div>
                <div class="mb-3 form-group position-relative has-icon-left">
                    <input required type="password" wire:model.blur='password_confirmation' class="form-control form-control-xl" placeholder="Konfirmasi Password Baru">
                    <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                </div>
                <button type="submit" class="mt-3 shadow-lg btn btn-primary btn-block btn-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target='resetPassword'>Simpan Password</span>
                    <span wire:loading wire:target='resetPassword' class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </form>
        @endif
    @else
        <div class="alert alert-success mt-5">{{ session('success') }}</div>
        <p class="mt-3 text-center"><a href="{{ route('masuk') }}" class="btn btn-primary">Kembali ke Login</a></p>
    @endif
</div>