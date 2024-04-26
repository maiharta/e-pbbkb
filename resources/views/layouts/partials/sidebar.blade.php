<div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-evenly align-items-center">
            <div class="logo">
                <a href="#">
                    <img src="{{ asset('assets/images/logo.svg') }}">
                </a>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Menu</li>
            @role('administrator')
                <li class="sidebar-item {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="{{ route('dashboard') }}">
                        <i class="isax isax-element-4"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item has-sub {{ request()->routeIs('verifikasi*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="#">
                        <i class="isax isax-verify"></i>
                        <span>Verifikasi</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->routeIs('verifikasi.user*') ? 'active' : '' }}">
                            <a class="submenu-link"
                               href="{{ route('verifikasi.user.index') }}">User</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item has-sub {{ request()->routeIs('master-data*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="#">
                        <i class="isax isax-forward-item"></i>
                        <span>Master Data</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->routeIs('master-data.sektor*') ? 'active' : '' }}">
                            <a class="submenu-link"
                               href="{{ route('master-data.sektor.index') }}">Sektor</a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('master-data.jenis-bbm*') ? 'active' : '' }}">
                            <a class="submenu-link"
                               href="{{ route('master-data.jenis-bbm.index') }}">Jenis BBM</a>
                        </li>
                    </ul>
                </li>
            @endrole

            @role('operator')
                <li class="sidebar-item {{ request()->routeIs('penjualan*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="{{ route('penjualan.index') }}">
                        <i class="isax isax-element-4"></i>
                        <span>Penjualan</span>
                    </a>
                </li>
            @endrole
        </ul>
    </div>
</div>
