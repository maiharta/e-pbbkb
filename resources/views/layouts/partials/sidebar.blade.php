<div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-evenly align-items-center">
            <div class="logo">
                <a href="#">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </a>
            </div>
            {{-- <div class="caption align-self-center"> --}}
                {{-- <h3 class="sidebar-title mb-0">E-Samsat</h3> --}}
                {{-- <p class="sidebar-title-caption mb-0"></p> --}}
            {{-- </div> --}}
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Menu</li>
            <li class="sidebar-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="sidebar-link">
                    <i class="isax isax-element-4"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item has-sub {{ request()->routeIs('master-data*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link">
                    <i class="isax isax-money-time"></i>
                    <span>Master Data</span>
                </a>
                {{-- <ul class="submenu">
                    <li class="submenu-item {{ request()->routeIs('premi-asuransi.sikp*') ? 'active' : '' }}">
                        <a href="#" class="submenu-link">User</a>
                    </li>
                </ul> --}}
            </li>
            <li class="sidebar-item has-sub {{ request()->routeIs('premi-asuransi*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link">
                    <i class="isax isax-money-time"></i>
                    <span>Log Aktivitas</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item {{ request()->routeIs('premi-asuransi.sikp*') ? 'active' : '' }}">
                        <a href="#" class="submenu-link">Samsat</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('premi-asuransi.sikp*') ? 'active' : '' }}">
                        <a href="#" class="submenu-link">User</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('premi-asuransi.sikp*') ? 'active' : '' }}">
                        <a href="#" class="submenu-link">Info Samsat</a>
                    </li>
                    <li class="submenu-item {{ request()->routeIs('premi-asuransi.sikp*') ? 'active' : '' }}">
                        <a href="#" class="submenu-link">Info Kebijakan</a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item {{ request()->routeIs('pengaturan-sistem*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link">
                    <i class="isax isax-setting-2"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('pengaturan-sistem*') ? 'active' : '' }}">
                <a href="#" class="sidebar-link">
                    <i class="isax isax-setting-2"></i>
                    <span>Pengaturan Sistem</span>
                </a>
            </li>
        </ul>
    </div>
</div>
