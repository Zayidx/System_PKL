<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ Request::routeIs('staffhubin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('staffhubin.dashboard') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub {{ Request::routeIs('staffhubin.master.*') ? 'active' : '' }}">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-archive-fill"></i>
            <span>Master Data</span>
        </a>

        <ul class="submenu {{ Request::routeIs('staff-hubin.master.*') ? 'active' : '' }}">
            <li class="submenu-item {{ Request::routeIs('staffhubin.master.pengajuan') ? 'active' : '' }}">
                <a href="{{ route('staffhubin.master.pengajuan') }}" wire:navigate class="submenu-link">Pengelolaan pengajuan</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('staffhubin.master.mitra-perusahaan') ? 'active' : '' }}">
                <a href="{{ route('staffhubin.master.mitra-perusahaan') }}" wire:navigate class="submenu-link">Mitra Perusahaan (Konfirmasi)</a>
            </li>
        </ul>
    </li>

    <livewire:auth.logout>
</ul>
