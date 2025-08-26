@extends('layouts.agent')

@section('title', $laundry->name)

@section('content')
<div class="dashboard-content">
    <!-- Hero Section with Logo -->
    <div class="laundry-hero">
        <div class="hero-background"></div>
        <div class="hero-content">
            <div class="laundry-logo-section">
                @if($laundry->logo)
                    <img src="{{ Storage::url($laundry->logo) }}" alt="{{ $laundry->name }}" class="laundry-logo">
                @else
                    <div class="logo-placeholder">
                        <i class="fas fa-tshirt"></i>
                    </div>
                @endif
            </div>
            <div class="laundry-info">
                <h1 class="laundry-title">{{ $laundry->name }}</h1>
                <p class="laundry-location">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ $laundry->city->name }}
                </p>
                <div class="laundry-status">
                    <span class="status-badge {{ $laundry->status }}">
                        {{ __('dashboard.' . ucfirst($laundry->status)) }}
                    </span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="{{ route('agent.laundries') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('dashboard.Back to Laundries') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="laundry-details-grid">
        <!-- Left Column -->
        <div class="details-left">
            <!-- Basic Information -->
            <div class="detail-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3>{{ __('dashboard.Basic Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Name') }}</span>
                                <span class="info-value">{{ $laundry->name }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Owner') }}</span>
                                <span class="info-value">{{ $laundry->user->name }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Email') }}</span>
                                <span class="info-value">{{ $laundry->user->email }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Phone') }}</span>
                                <span class="info-value">{{ $laundry->phone ?? $laundry->user->phone ?? __('dashboard.No phone') }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Joined') }}</span>
                                <span class="info-value">{{ $laundry->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="detail-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>{{ __('dashboard.Location Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-city"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.City') }}</span>
                                <span class="info-value">{{ $laundry->city->name }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Region') }}</span>
                                <span class="info-value">{{ $laundry->city->region ?? __('dashboard.Region not specified') }}</span>
                            </div>
                        </div>
                        @if($laundry->address)
                        <div class="info-item full-width">
                            <div class="info-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">{{ __('dashboard.Address') }}</span>
                                <span class="info-value">{{ $laundry->address }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="details-right">
            <!-- Working Hours -->
            <div class="detail-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>{{ __('dashboard.Working Hours') }}</h3>
                </div>
                <div class="card-body">
                    <div class="working-hours-grid">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div class="day-card {{ $day }}">
                                <div class="day-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="day-info">
                                    <span class="day-name">{{ __('dashboard.' . ucfirst($day)) }}</span>
                                    <span class="day-hours">
                                        {{ $laundry->working_hours[$day][0] ?? '09:00' }} - {{ $laundry->working_hours[$day][1] ?? '18:00' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Services & Features -->
            <div class="detail-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>{{ __('dashboard.Services & Features') }}</h3>
                </div>
                <div class="card-body">
                    <div class="features-grid">
                        <div class="feature-card {{ $laundry->delivery_available ? 'available' : 'unavailable' }}">
                            <div class="feature-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="feature-info">
                                <h4>{{ __('dashboard.Delivery Service') }}</h4>
                                <span class="feature-status">
                                    {{ $laundry->delivery_available ? __('dashboard.Available') : __('dashboard.Not Available') }}
                                </span>
                            </div>
                        </div>
                        <div class="feature-card {{ $laundry->pickup_available ? 'available' : 'unavailable' }}">
                            <div class="feature-icon">
                                <i class="fas fa-hand-paper"></i>
                            </div>
                            <div class="feature-info">
                                <h4>{{ __('dashboard.Pickup Service') }}</h4>
                                <span class="feature-status">
                                    {{ $laundry->pickup_available ? __('dashboard.Available') : __('dashboard.Not Available') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    @if($laundry->services && $laundry->services->count() > 0)
    <div class="services-section">
        <div class="section-header">
            <h2>{{ __('dashboard.Available Services') }}</h2>
            <p>{{ __('dashboard.Services offered by this laundry') }}</p>
        </div>
        <div class="services-grid">
            @foreach($laundry->services as $service)
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div class="service-status">
                            <span class="status-badge {{ $service->status }}">
                                {{ __('dashboard.' . ucfirst($service->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="service-body">
                        <h4>{{ $service->name }}</h4>
                        <p>{{ $service->description }}</p>
                    </div>
                    <div class="service-footer">
                        @if($service->price)
                            <div class="price-item">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>{{ $service->price }} {{ __('dashboard.SAR') }}</span>
                            </div>
                        @endif
                        @if($service->coin_cost)
                            <div class="price-item">
                                <i class="fas fa-coins"></i>
                                <span>{{ $service->coin_cost }} {{ __('dashboard.Coins') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
/* Hero Section */
.laundry-hero {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 3rem;
    color: white;
}

.laundry-logo-section {
    flex-shrink: 0;
}

.laundry-logo {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.2);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.laundry-logo:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.logo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    background: rgba(255,255,255,0.1);
    border: 4px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: rgba(255,255,255,0.7);
    backdrop-filter: blur(10px);
}

.laundry-info {
    flex: 1;
}

.laundry-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.laundry-location {
    font-size: 1.2rem;
    margin: 0 0 1rem 0;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.laundry-location i {
    color: #ffd700;
}

.laundry-status {
    display: inline-block;
}

.hero-actions {
    flex-shrink: 0;
}

/* Main Grid Layout */
.laundry-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 3rem;
}

/* Detail Cards */
.detail-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.detail-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.card-header .card-icon {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
}

.card-body {
    padding: 2rem;
}

/* Info Grid */
.info-grid {
    display: grid;
    gap: 1.5rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 15px;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.info-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.info-item.full-width {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
}

.info-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.info-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}

/* Working Hours Grid */
.working-hours-grid {
    display: grid;
    gap: 1rem;
}

.day-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 15px;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.day-card:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.day-card.monday { border-left: 4px solid #ff6b6b; }
.day-card.tuesday { border-left: 4px solid #4ecdc4; }
.day-card.wednesday { border-left: 4px solid #45b7d1; }
.day-card.thursday { border-left: 4px solid #96ceb4; }
.day-card.friday { border-left: 4px solid #feca57; }
.day-card.saturday { border-left: 4px solid #ff9ff3; }
.day-card.sunday { border-left: 4px solid #54a0ff; }

.day-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.day-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.day-name {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
}

.day-hours {
    color: #666;
    font-family: 'Courier New', monospace;
    font-weight: 500;
    font-size: 0.9rem;
}

/* Features Grid */
.features-grid {
    display: grid;
    gap: 1rem;
}

.feature-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 15px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.feature-card.available {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #28a745;
}

.feature-card.unavailable {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    border-color: #dc3545;
}

.feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.feature-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.feature-card.available .feature-icon {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.feature-card.unavailable .feature-icon {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
}

.feature-info {
    flex: 1;
}

.feature-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #333;
}

.feature-status {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.feature-card.available .feature-status {
    color: #155724;
}

.feature-card.unavailable .feature-status {
    color: #721c24;
}

/* Services Section */
.services-section {
    margin-top: 3rem;
}

.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    font-size: 2rem;
    font-weight: 800;
    color: #333;
    margin: 0 0 0.5rem 0;
}

.section-header p {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.service-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.service-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
}

.service-icon {
    width: 50px;
    height: 50px;
    border-radius: 15px;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.service-body {
    padding: 2rem;
}

.service-body h4 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
    margin: 0 0 1rem 0;
}

.service-body p {
    color: #666;
    line-height: 1.6;
    margin: 0;
}

.service-footer {
    padding: 0 2rem 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.price-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
}

.price-item:first-child {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.price-item:last-child {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1976d2;
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 2px solid transparent;
}

.status-badge.online {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-color: #28a745;
}

.status-badge.offline {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-color: #dc3545;
}

.status-badge.maintenance {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border-color: #ffc107;
}

.status-badge.active {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-color: #28a745;
}

.status-badge.inactive {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-color: #dc3545;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .laundry-details-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .laundry-title {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .hero-content {
        padding: 2rem;
    }
    
    .laundry-logo, .logo-placeholder {
        width: 100px;
        height: 100px;
    }
    
    .laundry-title {
        font-size: 1.8rem;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .service-footer {
        flex-direction: column;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .info-item {
        padding: 0.75rem;
    }
}
</style>
@endpush
