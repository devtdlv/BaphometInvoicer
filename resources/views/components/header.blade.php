<nav style="background: var(--bg-secondary); border-bottom: 1px solid var(--border); padding: 1rem 0; position: relative;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; height: 100%;">
        <div style="display: flex; align-items: center; gap: 2rem; height: 100%;">
            <a href="{{ auth()->check() ? route('dashboard') : '/' }}" style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); text-decoration: none; display: flex; align-items: center;">
                BaphometInvoicer
            </a>
            @auth
            @php
                $navLink = function ($routePatterns) {
                    $patterns = (array) $routePatterns;
                    $isActive = false;
                    foreach ($patterns as $pattern) {
                        if (request()->routeIs($pattern)) {
                            $isActive = true;
                            break;
                        }
                    }
                    $color = $isActive ? 'var(--accent)' : 'var(--text-secondary)';
                    $bg = $isActive ? 'background: rgba(91, 33, 182, 0.1);' : '';
                    $fontWeight = $isActive ? '600' : '500';
                    return "color: {$color}; text-decoration: none; font-weight: {$fontWeight}; transition: all 0.2s; display: flex; align-items: center; padding: 0.5rem 1rem; border-radius: 0.5rem; {$bg}";
                };
            @endphp
            <div style="display: flex; gap: 0.5rem; align-items: center; height: 100%;">
                <a href="{{ route('dashboard') }}" class="nav-link" style="{{ $navLink('dashboard') }}">Dashboard</a>
                <a href="{{ route('invoices.index') }}" class="nav-link" style="{{ $navLink(['invoices.*']) }}">Invoices</a>
                <a href="{{ route('quotes.index') }}" class="nav-link" style="{{ $navLink(['quotes.*']) }}">Quotes</a>
                <a href="{{ route('clients.index') }}" class="nav-link" style="{{ $navLink(['clients.*']) }}">Clients</a>
                <a href="{{ route('reports.index') }}" class="nav-link" style="{{ $navLink(['reports.*']) }}">Reports</a>
            </div>
            @endauth
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            @auth
                <div class="user-menu">
                    <button class="user-menu-button" id="userMenuButton" type="button">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                        <span class="dropdown-arrow"></span>
                    </button>
                    <div class="user-menu-dropdown" id="userMenuDropdown">
                        <a href="{{ route('settings.index') }}" class="user-menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="display: block;">
                            @csrf
                            <button type="submit" class="user-menu-item logout">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>

