<div id="auth-left">
    <a href="#" class="logo mb-4">
        <i class="fas fa-graduation-cap"></i> InfoPKL
    </a>
    <h1 class="mt-5 auth-title">Selamat datang di InfoPKL</h1>
    <p class="mb-5 auth-subtitle pe-5">
        Login menggunakan email & password yang benar 👋
    </p>

    <form wire:submit='login'>
        {{-- Menampilkan pesan error kredensial di atas form --}}
        @if ($errors->has('credentials'))
            <div class="mb-3 alert alert-danger">
                {{ $errors->first('credentials') }}
            </div>
        @endif

        <div class="mb-4 form-group position-relative has-icon-left">
            {{-- Menggunakan wire:model.blur untuk efisiensi --}}
            <input required type="email" wire:model.blur='email' class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email">
            <div class="form-control-icon">
                <i class="bi bi-person"></i>
            </div>
            @error('email')
                <div class="invalid-feedback">
                    <i class="bx bx-radio-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div x-data="{ show: false }" class="mb-3 form-group position-relative has-icon-left">
            <input required :type="show ? 'text' : 'password'" class="form-control form-control-xl @error('password') is-invalid @enderror"
                placeholder="Password" wire:model='password'>
            <div class="form-control-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div class="form-control-icon" style="left:auto; right:0; cursor: pointer;" @click="show = !show">
                <i :class="!show ? 'bi-eye-slash' : 'bi-eye'"></i>
            </div>
             @error('password')
                <div class="invalid-feedback">
                    <i class="bx bx-radio-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="mt-3 shadow-lg btn btn-primary btn-block btn-lg">
            <span wire:loading.remove wire:target='login'>Log in</span> 
            <span wire:loading wire:target='login' class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </button>
        
        <p class="m-0 mt-5 text-xl text-center text-dark">Belum punya akun? Sini <a href="{{ route('register') }}"
            class="text-primary fw-bold text-decoration-underline" wire:navigate>Daftar dulu</a></p> 
    </form>
</div>
