<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ Request::routeIs('administrator.dasbor') ? 'active' : '' }}">
        <a href="{{ route('administrator.dasbor') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item has-sub {{ Request::routeIs('administrator.data.*') ? 'active' : '' }}">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-archive-fill"></i>
            <span>Master Data</span>
        </a>

        <ul class="submenu {{ Request::routeIs('administrator.data.*') ? 'active' : '' }}">
            <li class="submenu-item {{ Request::routeIs('administrator.data.pengguna') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.pengguna') }}" wire:navigate class="submenu-link">Pengelolaan Pengguna</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('administrator.data.perusahaan') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.perusahaan') }}" wire:navigate class="submenu-link">Pengelolaan Perusahaan</a>
            </li>
              <li class="submenu-item {{ Request::routeIs('administrator.data.siswa') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.siswa') }}" wire:navigate class="submenu-link">Pengelolaan Siswa</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('administrator.data.kelas') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.kelas') }}" wire:navigate class="submenu-link">Pengelolaan kelas</a>
            </li>
                   <li class="submenu-item {{ Request::routeIs('administrator.data.jurusan') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.jurusan') }}" wire:navigate class="submenu-link">Pengelolaan jurusan</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('administrator.data.guru') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.guru') }}" wire:navigate class="submenu-link">Pengelolaan guru</a>
            </li>
              <li class="submenu-item {{ Request::routeIs('administrator.data.wali-kelas') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.wali-kelas') }}" wire:navigate class="submenu-link">Pengelolaan walikelas</a>
            </li>
              </li>
              <li class="submenu-item {{ Request::routeIs('administrator.data.pembimbing-perusahaan') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.pembimbing-perusahaan') }}" wire:navigate class="submenu-link">Pengelolaan pembimbing-perusahaan</a>
            </li>
            <li class="submenu-item {{ Request::routeIs('administrator.data.pembimbing-sekolah') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.pembimbing-sekolah') }}" wire:navigate class="submenu-link">Pengelolaan pembimbing-sekolah</a>
            </li>
                <li class="submenu-item {{ Request::routeIs('administrator.data.staf-hubin') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.staf-hubin') }}" wire:navigate class="submenu-link">Pengelolaan Staf Hubin</a>
            </li>
             <li class="submenu-item {{ Request::routeIs('administrator.data.kepala-program') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.kepala-program') }}" wire:navigate class="submenu-link">Pengelolaan kepala-program</a>
            </li>
             <li class="submenu-item {{ Request::routeIs('administrator.data.kepala-sekolah') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.kepala-sekolah') }}" wire:navigate class="submenu-link">Pengelolaan kepala-sekolah</a>
            </li>
             <li class="submenu-item {{ Request::routeIs('administrator.data.kompetensi') ? 'active' : '' }}">
                <a href="{{ route('administrator.data.kompetensi') }}" wire:navigate class="submenu-link">
        
                    Pengelolaan Kompetensi PKL
                </a>
            </li>
           
        </ul>
    </li>

    <livewire:autentikasi.keluar>
</ul>
