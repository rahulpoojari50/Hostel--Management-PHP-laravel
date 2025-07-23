<nav class="navbar navbar-expand-lg navbar-light bg-white shadow mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Hostel Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    @if(auth()->user()->role === 'warden')
                        <li class="nav-item"><a class="nav-link" href="{{ route('warden.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('warden.applications.index') }}">Applications</a></li>
                    @elseif(auth()->user()->role === 'student')
                        <li class="nav-item"><a class="nav-link" href="{{ route('student.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('student.hostels.index') }}">Browse Hostels</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('student.profile.edit') }}">Profile</a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="btn btn-outline-primary me-2" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-primary" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
