<ul class="menu">
    <li class="sidebar-title">Menu</li>

    <li class="sidebar-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
        <!-- Arahkan ke rute dashboard admin yang benar -->
        <a href="{{ route('admin.dashboard') }}" wire:navigate class='sidebar-link'>
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- Master Setting --}}
    <!-- Gunakan 'admin.master-user.*' untuk mencocokkan SEMUA rute di dalam grup master-user -->
    <li class="sidebar-item has-sub {{ Request::routeIs('admin.master-user.*') ? 'active' : '' }}">
        <a href="#" class='sidebar-link'>
            <i class="bi bi-person-gear"></i>
            <span>Master Settings</span>
        </a>

        <ul class="submenu">
            <!-- Gunakan nama rute yang benar: 'admin.master-user.users' -->
            <li class="submenu-item">
                <a href="{{ route('admin.master-user.users') }}" wire:navigate class="submenu-link">Config Settings</a>
            </li>
        </ul>
    </li>
    <li class="sidebar-item">
        {{-- Form ini akan mengirim request POST ke route 'logout' saat link di dalamnya diklik --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); this.closest('form').submit();"
               class='sidebar-link'>
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </form>
    </li>
</ul>
