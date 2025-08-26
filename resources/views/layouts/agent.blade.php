<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('dashboard.Agent Dashboard')) - موج</title>
    <link rel="stylesheet" href="{{ asset('dashboard/styles.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <span>موج</span>
            </div>
            
                        <nav class="nav-menu">
                <a href="{{ route('agent.dashboard') }}" class="nav-item {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}" data-page="home">
                    <i class="fas fa-th-large"></i>
                    <span>{{__('dashboard.Dashboard') }}</span>
                </a>
                <a href="{{ route('agent.profile') }}" class="nav-item {{ request()->routeIs('agent.profile') ? 'active' : '' }}" data-page="profile">
                    <i class="fas fa-user-edit"></i>
                    <span>{{ __('dashboard.Profile') }}</span>
                </a>
                <a href="{{ route('agent.cities') }}" class="nav-item {{ request()->routeIs('agent.cities') ? 'active' : '' }}" data-page="cities">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ __('dashboard.My Cities') }}</span>
                </a>
                <a href="{{ route('agent.laundries') }}" class="nav-item {{ request()->routeIs('agent.laundries*') ? 'active' : '' }}" data-page="laundries">
                    <i class="fas fa-tshirt"></i>
                    <span>{{ __('dashboard.Laundries') }}</span>
                </a>
                <form method="POST" action="{{ route('agent.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-item logout" style="background: none; border: none; width: 100%; text-align: right; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ __('dashboard.Logout') }}</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="admin-info">
                    <div class="admin-details">
                        <span class="admin-title">{{ __('dashboard.Agent') }}</span>
                        <span class="admin-email">{{ Auth::user()->email }}</span>
                    </div>
                                        <div class="header-actions">
                        <!-- Language Switcher -->
                       
                        <div class="admin-avatar">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('dashboard/script.js') }}"></script>
    @stack('scripts')
</body>
</html>
