@extends('layouts.agent')

@section('title', __('dashboard.My Cities'))

@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <h1>{{ __('dashboard.My Cities') }}</h1>
        <p>{{ __('dashboard.Manage the cities you serve') }}</p>
    </div>

    <div class="form-section">
        <form method="POST" action="{{ route('agent.updateCities') }}" class="form-container">
            @csrf
            
            <div class="form-group">
                <h3>{{ __('dashboard.Select Cities') }}</h3>
                <p class="form-description">{{ __('dashboard.Choose the cities where you provide services') }}</p>
                
                <div class="cities-selection">
                    <div class="cities-grid">
                        @foreach($allCities as $city)
                            <label class="city-checkbox">
                                <input type="checkbox" name="service_areas[]" value="{{ $city->id }}" 
                                       {{ in_array($city->id, old('service_areas', $agentCities->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <div class="city-card">
                                    <div class="city-info">
                                        <h4>{{ $city->name }}</h4>
                                        <p>{{ $city->region ?? __('dashboard.Region not specified') }}</p>
                                    </div>
                                    <div class="checkbox-indicator">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                @error('service_areas')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('dashboard.Update Cities') }}
                </button>
                <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('dashboard.Back to Dashboard') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Current Cities -->
    @if($agentCities->count() > 0)
    <div class="orders-card">
        <div class="card-header">
            <i class="fas fa-map-marker-alt"></i>
            <h3>{{ __('dashboard.Current Cities') }}</h3>
        </div>
        <p class="card-subtitle">{{ __('dashboard.Cities you currently serve') }}</p>
        <div class="orders-list">
            @foreach($agentCities as $city)
                <div class="order-item">
                    <div class="order-info">
                        <div class="order-price">
                            <span class="riyal-symbol">{{ $city->id }}</span>
                        </div>
                        <div class="laundry-name">{{ $city->name }}</div>
                        <div class="order-details">
                            {{ $city->region ?? __('dashboard.Region not specified') }}
                        </div>
                        <div class="order-time">{{ __('dashboard.City ID') }}</div>
                    </div>
                    <a href="{{ route('agent.laundries', ['city_id' => $city->id]) }}" class="status online">
                        {{ __('dashboard.View Laundries') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.cities-selection {
    margin: 1rem 0;
}

.cities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.city-checkbox {
    cursor: pointer;
    display: block;
}

.city-checkbox input[type="checkbox"] {
    display: none;
}

.city-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.city-checkbox input[type="checkbox"]:checked + .city-card {
    border-color: #007bff;
    background: #f8f9ff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.city-info h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-size: 1.1rem;
}

.city-info p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.checkbox-indicator {
    width: 24px;
    height: 24px;
    border: 2px solid #ddd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.city-checkbox input[type="checkbox"]:checked + .city-card .checkbox-indicator {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.city-checkbox input[type="checkbox"]:checked + .city-card .checkbox-indicator i {
    opacity: 1;
}

.checkbox-indicator i {
    opacity: 0;
    font-size: 12px;
    transition: opacity 0.3s ease;
}

.current-cities-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
}

.current-cities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.current-city-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.city-header h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
    font-size: 1.1rem;
}

.city-region {
    color: #666;
    font-size: 0.9rem;
}

.city-actions {
    margin-top: 1rem;
}

.form-description {
    color: #666;
    margin-bottom: 1rem;
}


</style>
@endpush


