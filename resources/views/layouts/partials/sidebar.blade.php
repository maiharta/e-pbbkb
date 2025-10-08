<div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-evenly align-items-center">
            <div class="logo">
                <a href="#">
                    <img src="{{ asset('assets/images/logo.png') }}">
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
                        <li class="submenu-item {{ request()->routeIs('verifikasi.pelaporan*') ? 'active' : '' }}">
                            <a class="submenu-link"
                               href="{{ route('verifikasi.pelaporan.index') }}">Penginputan</a>
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
                        <li class="submenu-item {{ request()->routeIs('master-data.cuti*') ? 'active' : '' }}">
                            <a class="submenu-link"
                               href="{{ route('master-data.cuti.index') }}">Cuti</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item {{ request()->routeIs('laporan*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="{{ route('laporan.index') }}">
                        <i class="isax isax-document-text"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('pengaturan-sistem*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="{{ route('pengaturan-sistem.index') }}">
                        <i class="isax isax-setting-2"></i>
                        <span>Pengaturan Sistem</span>
                    </a>
                </li>
            @endrole

            @role('operator')
                <li class="sidebar-item {{ request()->routeIs('pelaporan*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="{{ route('pelaporan.index') }}">
                        <i class="isax isax-receipt"></i>
                        <span>Penginputan</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('invoices*') ? 'active' : '' }}">
                    <a class="sidebar-link"
                       href="{{ route('invoices.index') }}">
                        <i class="isax isax-transaction-minus"></i>
                        <span>Data Transaksi</span>
                    </a>
                </li>
            @endrole
        </ul>
    </div>
</div>
