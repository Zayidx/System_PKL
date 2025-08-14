{{--
    Ini adalah contoh file sidebar untuk PENGGUNA BIASA.
    Pastikan Anda memanggil file ini dari layout yang benar.
    Contoh: @include('components.layouts.partials.sidebar-user-dashboard')
--}}
<ul class="menu">
    <li class="sidebar-title">Menu Pengguna</li>

    <li class="sidebar-item {{ Request::routeIs('user.dashboard') ? 'active' : '' }}">
        {{-- Arahkan ke rute dashboard pengguna yang benar --}}
        <a href="{{ route('user.dashboard') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item {{ Request::routeIs('user.pengajuan*') ? 'active' : '' }}">
        {{-- Arahkan ke rute pengajuan pengguna yang benar --}}
        <a href="{{ route('user.pengajuan') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-send-fill"></i>
            <span>Pengajuan Magang</span>
        </a>
    </li>

    <li class="sidebar-item {{ Request::routeIs('user.riwayat-prakerin') ? 'active' : '' }}">
        {{-- Menu Riwayat Prakerin --}}
        <a href="{{ route('user.riwayat-prakerin') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-clock-history"></i>
            <span>Riwayat Prakerin</span>
        </a>
    </li>
    
    {{-- Tambahkan menu lain untuk user di sini jika ada --}}
    {{-- 
    <li class="sidebar-item {{ Request::routeIs('user.profile') ? 'active' : '' }}">
        <a href="#" wire:navigate class='sidebar-link'>
            <i class="bi bi-person-fill"></i>
            <span>Profil Saya</span>
        </a>
    </li>
    --}}
    
    <livewire:auth.logout>
</ul>
