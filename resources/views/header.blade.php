<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom px-4 py-2">
    <div class="container-fluid">
        
        <div class="navbar-text fw-medium text-secondary">
            <i class="bi bi-calendar3 me-2"></i> Date: <span class="text-dark">{{ now()->format('d-M-Y') }}</span>
        </div>

        <div class="d-flex align-items-center ms-auto">
            
            @if(Auth::check())
            <span class="badge bg-light text-dark border me-3 py-2 px-3 rounded-pill text-xs">
                <i class="bi bi-shield-lock-fill text-primary me-1"></i> 
                {{ Auth::user()->roles->pluck('name')->first() ?? 'No Role' }}
            </span>
            @endif

            <div class="dropdown">
                <a class="btn btn-light btn-sm dropdown-toggle border d-flex align-items-center gap-2 py-1.5 px-3 rounded" 
                   href="#" role="button" id="headerProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-6 text-secondary"></i>
                    <strong>{{ Auth::user()->name ?? 'Guest' }}</strong>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="headerProfileDropdown">
                    <li><a class="dropdown-item px-3 py-2 text-sm text-secondary" href="#"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
                    <li><hr class="dropdown-divider mx-2"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item px-3 py-2 text-sm text-danger"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
