<div id="auth-left">
    <a href="#" class="mb-4 logo">
        <i class="fas fa-graduation-cap"></i> InfoPKL
    </a>
    
    @if (!$showOtpForm)
        {{-- Tampilan untuk form login awal --}}
        <h1 class="mt-5 auth-title">Selamat datang di InfoPKL</h1>
        <p class="mb-5 auth-subtitle pe-5">
            Login menggunakan email & password yang benar 👋
        </p>
        
        <form wire:submit='attemptLogin' novalidate>
            @if ($errors->has('credentials'))
                <div class="mb-3 alert alert-danger">{{ $errors->first('credentials') }}</div>
            @endif

            <div class="mb-4 form-group position-relative has-icon-left">
                <input required type="email" wire:model.blur='email' class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email">
                <div class="form-control-icon"><i class="bi bi-person"></i></div>
                @error('email')<div class="invalid-feedback"><i class="bx bx-radio-circle"></i> {{ $message }}</div>@enderror
            </div>

            <div x-data="{ show: false }" class="mb-3 form-group position-relative has-icon-left">
                <input required :type="show ? 'text' : 'password'" class="form-control form-control-xl @error('password') is-invalid @enderror" placeholder="Password" wire:model='password'>
                <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                <div class="form-control-icon" style="left:auto; right:0; cursor: pointer;" @click="show = !show">
                    <i :class="!show ? 'bi-eye-slash' : 'bi-eye'"></i>
                </div>
                 @error('password')<div class="invalid-feedback"><i class="bx bx-radio-circle"></i> {{ $message }}</div>@enderror
            </div>

            <button type="submit" class="mt-3 shadow-lg btn btn-primary btn-block btn-lg" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target='attemptLogin'>Log in</span> 
                <span wire:loading wire:target='attemptLogin' class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </button>
            
            <p class="m-0 mt-5 text-xl text-center text-dark">Belum punya akun? Sini <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-underline" wire:navigate>Daftar dulu</a></p> 
        </form>

    @else
        {{-- Tampilan untuk form verifikasi OTP --}}
        <h1 class="mt-5 auth-title">Verifikasi Akun Anda</h1>
        <p class="mb-5 auth-subtitle pe-5">
            Kami telah mengirimkan kode OTP ke email <strong>{{ $email }}</strong>. Silakan periksa kotak masuk Anda.
        </p>
        
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
            
            <form wire:submit='verifyOtpAndLogin'>
                @if ($errors->has('otp'))
                    <div class="mb-3 alert alert-danger">{{ $errors->first('otp') }}</div>
                @endif

                <div class="mb-4 form-group position-relative has-icon-left">
                    <input required type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" wire:model.blur='otp' class="form-control form-control-xl @error('otp') is-invalid @enderror" placeholder="Masukkan 6 digit OTP">
                    <div class="form-control-icon"><i class="bi bi-key"></i></div>
                    @error('otp')<div class="invalid-feedback"><i class="bx bx-radio-circle"></i> {{ $message }}</div>@enderror
                </div>

                <button type="submit" class="mt-3 shadow-lg btn btn-primary btn-block btn-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target='verifyOtpAndLogin'>Verifikasi & Login</span> 
                    <span wire:loading wire:target='verifyOtpAndLogin' class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
            </form>

            <div class="mt-4 text-center">
                <template x-if="timerRunning">
                    <p class="text-muted">Kirim ulang kode dalam <span x-text="countdown" class="fw-bold"></span> detik</p>
                </template>
                <template x-if="!timerRunning">
                    <p class="text-muted">Tidak menerima kode? <a href="#" wire:click.prevent="resendOtp" class="font-bold">Kirim Ulang</a></p>
                </template>
                <a href="#" wire:click.prevent="cancelOtp" class="text-muted d-block mt-2">Salah akun? Kembali</a>
            </div>
        </div>
    @endif
</div>
