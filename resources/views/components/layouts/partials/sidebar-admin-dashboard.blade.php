<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub {{ Request::routeIs('admin.master.*') ? 'active' : '' }}">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-archive-fill"></i>
            <span>Master Data</span>
        </a>

        <ul class="submenu {{ Request::routeIs('admin.master.*') ? 'active' : '' }}">
            <li class="submenu-item {{ Request::routeIs('admin.master.users') ? 'active' : '' }}">
                <a href="{{ route('admin.master.users') }}" wire:navigate class="submenu-link">Pengelolaan Pengguna</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('admin.master.perusahaan') ? 'active' : '' }}">
                <a href="{{ route('admin.master.perusahaan') }}" wire:navigate class="submenu-link">Pengelolaan Perusahaan</a>
            </li>
              <li class="submenu-item {{ Request::routeIs('admin.master.siswa') ? 'active' : '' }}">
                <a href="{{ route('admin.master.siswa') }}" wire:navigate class="submenu-link">Pengelolaan Siswa</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('admin.master.kelas') ? 'active' : '' }}">
                <a href="{{ route('admin.master.kelas') }}" wire:navigate class="submenu-link">Pengelolaan kelas</a>
            </li>
                   <li class="submenu-item {{ Request::routeIs('admin.master.jurusan') ? 'active' : '' }}">
                <a href="{{ route('admin.master.jurusan') }}" wire:navigate class="submenu-link">Pengelolaan jurusan</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('admin.master.guru') ? 'active' : '' }}">
                <a href="{{ route('admin.master.guru') }}" wire:navigate class="submenu-link">Pengelolaan guru</a>
            </li>
              <li class="submenu-item {{ Request::routeIs('admin.master.walikelas') ? 'active' : '' }}">
                <a href="{{ route('admin.master.walikelas') }}" wire:navigate class="submenu-link">Pengelolaan walikelas</a>
            </li>
            
        </ul>
    </li>

    <livewire:auth.logout>
</ul>
