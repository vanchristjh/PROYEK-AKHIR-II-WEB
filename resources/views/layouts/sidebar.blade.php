<div class="sidebar">    <div class="logo" style="padding: 20px; text-align: center;">
        <img src="{{ asset('assets/images/logo.jpg') }}" alt="SMAN 1 Girsang Sipangan Bolon" style="width: 100px; height: 100px; display: block; object-fit: cover; border-radius: 10%; margin: 0 auto 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <h2 style="color: white; font-weight: bold; margin-top: 5px;">SMAN 1 Girsang Sipangan Bolon</h2>
        <p style="color: rgba(255,255,255,0.8);">E-Learning System</p>
    </div>

    <div class="sidebar-menu">
        <div class="menu-section">
            <h3><i class="fas fa-tachometer-alt sidebar-icon"></i> DASHBOARD</h3>
            <ul>
                <li>
                    @if(Route::has('admin.dashboard'))
                        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home sidebar-icon"></i> Dashboard</a>
                    @else
                        <a href="#" class="disabled"><i class="fas fa-home sidebar-icon"></i> Dashboard</a>
                    @endif
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3><i class="fas fa-users sidebar-icon"></i> PENGGUNA</h3>
            <ul>
                <li>
                    @if(Route::has('admin.users'))
                        <a href="{{ route('admin.users') }}"><i class="fas fa-user sidebar-icon"></i> Pengguna</a>
                    @else
                        <a href="#" class="disabled"><i class="fas fa-user sidebar-icon"></i> Pengguna</a>
                    @endif
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3><i class="fas fa-book sidebar-icon"></i> AKADEMIK</h3>
            <ul>
                <li>
                    @if(Route::has('admin.subjects'))
                        <a href="{{ route('admin.subjects') }}"><i class="fas fa-book-open sidebar-icon"></i> Mata Pelajaran</a>
                    @else
                        <a href="#" class="disabled"><i class="fas fa-book-open sidebar-icon"></i> Mata Pelajaran</a>
                    @endif
                </li>
                <li>
                    <a href="{{ route('admin.class') }}" class="sidebar-link {{ request()->routeIs('admin.class*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Kelas</span>
                    </a>
                </li>
                <li>
                    @if(auth()->user()->role->name == 'admin')
                        <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar"></i>
                            <p>Jadwal</p>
                        </a>
                    @elseif(auth()->user()->role->name == 'siswa')
                        <a href="{{ route('siswa.schedules.index') }}" class="nav-link {{ request()->routeIs('siswa.schedules.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar"></i>
                            <p>Jadwal</p>
                        </a>
                    @elseif(auth()->user()->role->name == 'guru')
                        <a href="{{ route('guru.schedules.index') }}" class="nav-link {{ request()->routeIs('guru.schedules.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar"></i>
                            <p>Jadwal</p>
                        </a>
                    @endif
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3><i class="fas fa-comment sidebar-icon"></i> KOMUNIKASI</h3>
            <ul>
                <li>
                    @if(auth()->user()->role->name == 'admin')
                        @if(Route::has('admin.announcements.index'))
                            <a href="{{ route('admin.announcements.index') }}"><i class="fas fa-bullhorn sidebar-icon"></i> Pengumuman</a>
                        @else
                            <a href="#" class="disabled"><i class="fas fa-bullhorn sidebar-icon"></i> Pengumuman</a>
                        @endif
                    @elseif(auth()->user()->role->name == 'siswa')
                        @if(Route::has('siswa.announcements.index'))
                            <a href="{{ route('siswa.announcements.index') }}"><i class="fas fa-bullhorn sidebar-icon"></i> Pengumuman</a>
                        @else
                            <a href="#" class="disabled"><i class="fas fa-bullhorn sidebar-icon"></i> Pengumuman</a>
                        @endif
                    @elseif(auth()->user()->role->name == 'guru')
                        @if(Route::has('guru.announcements.index'))
                            <a href="{{ route('guru.announcements.index') }}"><i class="fas fa-bullhorn sidebar-icon"></i> Pengumuman</a>
                        @else
                            <a href="#" class="disabled"><i class="fas fa-bullhorn sidebar-icon"></i> Pengumuman</a>
                        @endif
                    @endif
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3><i class="fas fa-cog sidebar-icon"></i> SISTEM & AKUN</h3>
            <ul>
                <li>
                    @if(auth()->user()->role->name == 'admin')
                        @if(Route::has('admin.profile'))
                            <a href="{{ route('admin.profile') }}"><i class="fas fa-user-circle sidebar-icon"></i> Profil Saya</a>
                        @else
                            <a href="#" class="disabled"><i class="fas fa-user-circle sidebar-icon"></i> Profil Saya</a>
                        @endif
                    @elseif(auth()->user()->role->name == 'siswa')
                        @if(Route::has('siswa.profile'))
                            <a href="{{ route('siswa.profile') }}"><i class="fas fa-user-circle sidebar-icon"></i> Profil Saya</a>
                        @else
                            <a href="#" class="disabled"><i class="fas fa-user-circle sidebar-icon"></i> Profil Saya</a>
                        @endif
                    @elseif(auth()->user()->role->name == 'guru')
                        @if(Route::has('guru.profile'))
                            <a href="{{ route('guru.profile') }}"><i class="fas fa-user-circle sidebar-icon"></i> Profil Saya</a>
                        @else
                            <a href="#" class="disabled"><i class="fas fa-user-circle sidebar-icon"></i> Profil Saya</a>
                        @endif
                    @endif
                </li>
            </ul>
        </div>
    </div>

    <div class="user-info">
        <div class="avatar">
            <span>A</span>
        </div>
        <div class="user-details">
            <h4>Admin User</h4>
            <p>Administrator</p>
        </div>
    </div>

    <div class="logout-button">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt sidebar-icon"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
