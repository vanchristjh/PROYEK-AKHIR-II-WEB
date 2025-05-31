<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-2">
    <div class="container-fluid px-4">
        <button class="navbar-toggler" type="button" id="sidebar-toggle">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-brand">
            @if(request()->is('admin*'))
                Admin Panel
            @elseif(request()->is('guru*'))
                Guru Panel
            @elseif(request()->is('siswa*'))
                Siswa Panel
            @endif
        </span>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ auth()->user()->name ?? 'User' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        @if(auth()->user() && auth()->user()->role)
                            @if(auth()->user()->role->name == 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                            @elseif(auth()->user()->role->name == 'guru')
                                <li><a class="dropdown-item" href="{{ route('guru.profile') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                            @elseif(auth()->user()->role->name == 'siswa')
                                <li><a class="dropdown-item" href="{{ route('siswa.profile') }}"><i class="fas fa-user me-2"></i> Profil</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>