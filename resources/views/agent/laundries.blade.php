@extends('layouts.agent')

@section('title', __('dashboard.Laundries'))

@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <h1>{{ __('dashboard.Laundries in My Cities') }}</h1>
        <p>{{ __('dashboard.View all laundries in your service areas') }}</p>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <div class="filters-header">
            <h3><i class="fas fa-filter"></i> {{ __('dashboard.Filters') }}</h3>
            <p>{{ __('dashboard.Filter laundries by city, status, or search term') }}</p>
        </div>
        <form method="GET" action="{{ route('agent.laundries') }}" class="filters-form">
            <div class="filter-group">
                <label class="filter-label">{{ __('dashboard.Search') }}</label>
                <div class="search-wrapper">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="{{ __('dashboard.Search laundries...') }}" class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
            
            <div class="filter-group">
                <label class="filter-label">{{ __('dashboard.City') }}</label>
                <select name="city_id" class="filter-select">
                    <option value="">{{ __('dashboard.All Cities') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">{{ __('dashboard.Status') }}</label>
                <select name="status" class="filter-select">
                    <option value="">{{ __('dashboard.All Statuses') }}</option>
                    <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>{{ __('dashboard.Online') }}</option>
                    <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>{{ __('dashboard.Offline') }}</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>{{ __('dashboard.Maintenance') }}</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    {{ __('dashboard.Apply Filters') }}
                </button>
                
                <a href="{{ route('agent.laundries') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    {{ __('dashboard.Clear') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Laundries List -->
    <div class="orders-card">
        <div class="card-header">
            <i class="fas fa-tshirt"></i>
            <h3>{{ __('dashboard.Laundries in My Cities') }}</h3>
        </div>
        <p class="card-subtitle">{{ __('dashboard.View all laundries in your service areas') }}</p>
        
        @if($laundries->count() > 0)
            <div class="laundries-grid">
                @foreach($laundries as $laundry)
                    <div class="laundry-card">
                        <div class="laundry-header">
                            <div class="laundry-logo">
                                @if($laundry->logo)
                                    <img src="{{ Storage::url($laundry->logo) }}" alt="{{ $laundry->name }}" class="logo-image">
                                @else
                                    <div class="logo-placeholder">
                                        <i class="fas fa-tshirt"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="laundry-status">
                                <span class="status-badge {{ $laundry->status }}">
                                    {{ __('dashboard.' . ucfirst($laundry->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="laundry-info">
                            <h3 class="laundry-name">{{ $laundry->name }}</h3>
                            <p class="laundry-owner">
                                <i class="fas fa-user"></i>
                                {{ $laundry->user->name }}
                            </p>
                            <p class="laundry-location">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $laundry->city->name }}
                            </p>
                        </div>

                        <div class="laundry-details">
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $laundry->phone ?? $laundry->user->phone ?? __('dashboard.No phone') }}</span>
                            </div>
                            
                            @if($laundry->address)
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ Str::limit($laundry->address, 30) }}</span>
                            </div>
                            @endif

                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $laundry->working_hours['monday'][0] ?? '09:00' }} - {{ $laundry->working_hours['monday'][1] ?? '18:00' }}</span>
                            </div>
                        </div>

                        <div class="laundry-features">
                            @if($laundry->delivery_available)
                                <span class="feature-badge">
                                    <i class="fas fa-truck"></i>
                                    {{ __('dashboard.Delivery') }}
                                </span>
                            @endif
                            
                            @if($laundry->pickup_available)
                                <span class="feature-badge">
                                    <i class="fas fa-hand-paper"></i>
                                    {{ __('dashboard.Pickup') }}
                                </span>
                            @endif
                        </div>

                        <div class="laundry-actions">
                            <a href="{{ route('agent.laundry.show', $laundry->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i>
                                {{ __('dashboard.View Details') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($laundries->hasPages())
                <div class="pagination-container">
                    {{ $laundries->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="no-orders">
                <i class="fas fa-tshirt"></i>
                <p>{{ __('dashboard.No laundries found in your service areas') }}</p>
                <a href="{{ route('agent.cities') }}" class="btn btn-primary">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ __('dashboard.Manage Cities') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.laundries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.laundry-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    position: relative;
    overflow: hidden;
}

.laundry-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
}

.laundry-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.laundry-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.laundry-logo {
    width: 80px;
    height: 80px;
    border-radius: 15px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #e9ecef;
    transition: all 0.3s ease;
}

.laundry-card:hover .laundry-logo {
    border-color: #4facfe;
    transform: scale(1.05);
}

.logo-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 2rem;
}

.laundry-status {
    flex-shrink: 0;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.online {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-badge.offline {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-badge.maintenance {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.laundry-info {
    margin-bottom: 1.5rem;
}

.laundry-name {
    margin: 0 0 0.75rem 0;
    color: #333;
    font-size: 1.4rem;
    font-weight: 700;
    line-height: 1.3;
}

.laundry-owner, .laundry-location {
    margin: 0 0 0.5rem 0;
    color: #666;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.laundry-owner i, .laundry-location i {
    color: #4facfe;
    width: 16px;
}

.laundry-details {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: #555;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item i {
    width: 18px;
    color: #4facfe;
    font-size: 1rem;
}

.laundry-features {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.feature-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.feature-badge i {
    font-size: 0.9rem;
}

.laundry-actions {
    display: flex;
    justify-content: center;
}

.laundry-actions .btn {
    padding: 0.75rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.laundry-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
}

.no-orders {
    text-align: center;
    padding: 4rem 2rem;
    color: #666;
}

.no-orders i {
    font-size: 5rem;
    color: #ddd;
    margin-bottom: 1.5rem;
}

.no-orders p {
    margin: 0 0 2rem 0;
    font-size: 1.1rem;
}

/* Enhanced Filters Styles */
.filters-header {
    margin-bottom: 1.5rem;
    text-align: center;
}

.filters-header h3 {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.filters-header h3 i {
    color: #4facfe;
}

.filters-header p {
    margin: 0;
    color: #666;
    font-size: 0.95rem;
}

.filter-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #555;
    font-size: 0.9rem;
}

.search-wrapper {
    position: relative;
}

.filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 1.5rem;
}

.filter-actions .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.filter-actions .btn:hover {
    transform: translateY(-2px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .laundries-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .laundry-card {
        padding: 1.5rem;
    }
    
    .laundry-logo {
        width: 60px;
        height: 60px;
    }
    
    .laundry-name {
        font-size: 1.2rem;
    }
    
    .filters-form {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .filter-actions {
        flex-direction: column;
    }
}
</style>
@endpush
