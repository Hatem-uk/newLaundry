@extends('layouts.agent')

@section('title', __('dashboard.Agent Dashboard'))

@section('content')
<div class="dashboard-content">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1>{{ __('dashboard.Welcome') }}, {{ Auth::user()->name }}!</h1>
        <p>{{ __('dashboard.Manage your laundry services and track your performance') }}</p>
        <div class="status-badge {{ Auth::user()->status === 'approved' ? 'status-active' : (Auth::user()->status === 'pending' ? 'status-pending' : 'status-inactive') }}">
            <i class="fas fa-circle"></i>
            {{ __('dashboard.Status') }}: {{ ucfirst(Auth::user()->status) }}
        </div>
    </div>

    <!-- Status Info -->
    <div class="status-info">
        <h4>{{ __('dashboard.Account Status') }}:</h4>
        @if(auth()->user()->status === 'pending')
            <div class="alert alert-warning">
                <i class="fas fa-clock"></i>
                {{ __('dashboard.Your account is under review. Please wait for admin approval.') }}
            </div>
        @elseif(auth()->user()->status === 'approved')
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ __('dashboard.Your account has been approved. You can now use all features.') }}
            </div>
        @elseif(auth()->user()->status === 'rejected')
            <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i>
                {{ __('dashboard.Your account has been rejected. Please contact admin for more information.') }}
            </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.My Cities') }}</h3>
                <div class="number">{{ $stats['total_cities'] }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-tshirt"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Total Laundries') }}</h3>
                <div class="number">{{ $stats['total_laundries'] }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Active Laundries') }}</h3>
                <div class="number">{{ $stats['active_laundries'] }}</div>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Offline Laundries') }}</h3>
                <div class="number">{{ $stats['offline_laundries'] }}</div>
            </div>
        </div>
    </div>

        <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>{{ __('dashboard.Quick Actions') }}</h2>
        <div class="actions-grid">
            <a href="{{ route('agent.profile') }}" class="action-btn">
                <i class="fas fa-user-edit"></i>
                <!-- <span>{{ __('dashboard.Edit Profile') }}</span> -->
            </a>
            <a href="{{ route('agent.cities') }}" class="action-btn">
                <i class="fas fa-map-marker-alt"></i>
                <!-- <span>{{ __('dashboard.Manage Cities') }}</span> -->
            </a>
            <a href="{{ route('agent.laundries') }}" class="action-btn">
                <i class="fas fa-tshirt"></i>
                <!-- <span>{{ __('dashboard.View Laundries') }}</span> -->
            </a>
        </div>
    </div>

    <!-- Recent Cities -->
    @if($cities->count() > 0)
    <div class="orders-card">
        <div class="card-header">
            <i class="fas fa-map-marker-alt"></i>
            <h3>{{ __('dashboard.My Cities') }}</h3>
        </div>
        <p class="card-subtitle">{{ __('dashboard.Cities you serve') }}</p>
        <div class="orders-list">
            @foreach($cities as $city)
                <div class="order-item">
                    <div class="order-info">
                        <div class="order-price">
                            <span class="riyal-symbol">{{ $laundries->where('city_id', $city->id)->count() }}</span>
                        </div>
                        <div class="laundry-name">{{ $city->name }}</div>
                        <div class="order-details">
                            {{ $city->region ?? __('dashboard.Region not specified') }}
                        </div>
                        <div class="order-time">{{ __('dashboard.Laundries') }}</div>
                    </div>
                    <span class="status online">
                        {{ __('dashboard.Active') }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript functionality here
        console.log('Agent dashboard loaded');
    });
</script>
@endpush
