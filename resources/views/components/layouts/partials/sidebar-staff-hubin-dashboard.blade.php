<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ Request::routeIs('staf-hubin.dasbor') ? 'active' : '' }}">
        <a href="{{ route('staf-hubin.dasbor') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub {{ Request::routeIs('staf-hubin.data.*') ? 'active' : '' }}">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-archive-fill"></i>
            <span>Master Data</span>
        </a>

        <ul class="submenu {{ Request::routeIs('staf-hubin.data.*') ? 'active' : '' }}">
            <li class="submenu-item {{ Request::routeIs('staf-hubin.data.pengajuan') ? 'active' : '' }}">
                <a href="{{ route('staf-hubin.data.pengajuan') }}" wire:navigate class="submenu-link">Pengelolaan pengajuan</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('staf-hubin.data.mitra-perusahaan') ? 'active' : '' }}">
                <a href="{{ route('staf-hubin.data.mitra-perusahaan') }}" wire:navigate class="submenu-link">Mitra Perusahaan (Konfirmasi)</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('staf-hubin.data.prakerin') }}" class="nav-link {{ request()->routeIs('staf-hubin.data.prakerin*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase me-2"></i>
                    <span>Prakerin</span>
                </a>
            </li>
        </ul>
    </li>

    <livewire:autentikasi.keluar>
</ul>
