<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', trans('dashboard.Dashboard')) - موج</title>
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
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-page="home">
                    <i class="fas fa-th-large"></i>
                    <span>{{ trans('dashboard.Dashboard') }}</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}" data-page="users">
                    <i class="fas fa-users"></i>
                    <span>{{ trans('dashboard.User Management') }}</span>
                </a>
                <a href="{{ route('admin.agents') }}" class="nav-item {{ request()->routeIs('admin.agents*') ? 'active' : '' }}" data-page="agents">
                    <i class="fas fa-user-tie"></i>
                    <span>{{ trans('dashboard.Agent Management') }}</span>
                </a>
                <a href="{{ route('admin.laundries') }}" class="nav-item {{ request()->routeIs('admin.laundries*') ? 'active' : '' }}" data-page="laundries">
                    <i class="fas fa-tshirt"></i>
                    <span>{{ trans('dashboard.Laundry Management') }}</span>
                </a>
                <a href="{{ route('admin.services') }}" class="nav-item {{ request()->routeIs('admin.services*') ? 'active' : '' }}" data-page="services">
                    <i class="fas fa-file-alt"></i>
                    <span>{{ trans('dashboard.Service Management') }}</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" data-page="orders">
                    <i class="fas fa-list"></i>
                    <span>{{ trans('dashboard.Orders') }}</span>
                </a>
                <a href="{{ route('admin.tracking') }}" class="nav-item {{ request()->routeIs('admin.tracking') ? 'active' : '' }}" data-page="tracking">
                    <i class="fas fa-truck"></i>
                    <span>{{ trans('dashboard.Order Tracking') }}</span>
                </a>
                <a href="{{ route('admin.profile.show') }}" class="nav-item {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>{{ trans('dashboard.Profile') }}</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-item logout" style="background: none; border: none; width: 100%; text-align: right; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{ trans('dashboard.Logout') }}</span>
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
                        <span class="admin-title">{{ trans('dashboard.Admin') }}</span>
                        <span class="admin-email">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="header-actions">
                        <!-- Language Switcher -->
                        <div class="language-switcher">
                            <a href="{{ route('language.switch', 'ar') }}" class="lang-btn {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                <i class="fas fa-globe"></i>
                                {{ trans('dashboard.Arabic') }}
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                <i class="fas fa-globe"></i>
                                {{ trans('dashboard.English') }}
                            </a>
                        </div>
                        <div class="admin-avatar">
                            <i class="fas fa-user"></i>
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
