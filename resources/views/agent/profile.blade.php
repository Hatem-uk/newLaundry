@extends('layouts.agent')

@section('title', __('dashboard.Profile'))

@section('content')
<div class="dashboard-content">
    <!-- Hero Section -->
    <div class="profile-hero">
        <div class="hero-background"></div>
        <div class="hero-content">
            <div class="profile-avatar">
                @if($agentProfile && $agentProfile->logo)
                    <img src="{{ Storage::url($agentProfile->logo) }}" alt="Profile Avatar" class="avatar-image">
                @else
                    <div class="avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <h1 class="profile-title">{{ __('dashboard.My Profile') }}</h1>
                <p class="profile-subtitle">{{ __('dashboard.Manage your agent profile and settings') }}</p>
                <div class="profile-status">
                    <span class="status-badge {{ $agentProfile->status ?? 'offline' }}">
                        {{ __('dashboard.' . ucfirst($agentProfile->status ?? 'offline')) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Form -->
    <form method="POST" action="{{ route('agent.updateProfile') }}" enctype="multipart/form-data" class="profile-form">
        @csrf
        
        <div class="profile-form-grid">
            <!-- Basic Information Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>{{ __('dashboard.Basic Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="name_ar">{{ __('dashboard.Name') }} ({{ __('dashboard.Arabic') }}) <span class="required">*</span></label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input type="text" id="name_ar" name="name_ar" 
                                       value="{{ old('name_ar', is_array($agentProfile->name ?? null) ? ($agentProfile->name['ar'] ?? '') : ($agentProfile->name ?? '')) }}" 
                                       class="form-input @error('name_ar') error @enderror" required>
                            </div>
                            @error('name_ar')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="name_en">{{ __('dashboard.Name') }} ({{ __('dashboard.English') }}) <span class="required">*</span></label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <input type="text" id="name_en" name="name_en" 
                                       value="{{ old('name_en', is_array($agentProfile->name ?? null) ? ($agentProfile->name['en'] ?? '') : ($agentProfile->name ?? '')) }}" 
                                       class="form-input @error('name_en') error @enderror" required>
                            </div>
                            @error('name_en')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="email">{{ __('dashboard.Email') }} <span class="required">*</span></label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email', $agent->email) }}" 
                                       class="form-input @error('email') error @enderror" required>
                            </div>
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="phone">{{ __('dashboard.Phone') }}</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $agent->phone) }}" 
                                       class="form-input @error('phone') error @enderror">
                            </div>
                            @error('phone')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="status">{{ __('dashboard.Agent Status') }} <span class="required">*</span></label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-toggle-on"></i>
                                </div>
                                <select id="status" name="status" class="form-select @error('status') error @enderror" required>
                                    <option value="online" {{ old('status', $agentProfile->status ?? '') == 'online' ? 'selected' : '' }}>{{ __('dashboard.Online') }}</option>
                                    <option value="offline" {{ old('status', $agentProfile->status ?? '') == 'offline' ? 'selected' : '' }}>{{ __('dashboard.Offline') }}</option>
                                    <option value="maintenance" {{ old('status', $agentProfile->status ?? '') == 'maintenance' ? 'selected' : '' }}>{{ __('dashboard.Maintenance') }}</option>
                                </select>
                            </div>
                            @error('status')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agent Information Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>{{ __('dashboard.Agent Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-field">
                            <label for="license_number">{{ __('dashboard.License Number') }}</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <input type="text" id="license_number" name="license_number" 
                                       value="{{ old('license_number', $agentProfile->license_number ?? '') }}" 
                                       class="form-input @error('license_number') error @enderror">
                            </div>
                            @error('license_number')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="logo">{{ __('dashboard.Logo') }}</label>
                            <div class="file-upload">
                                <input type="file" id="logo" name="logo" accept="image/*" 
                                       class="file-input @error('logo') error @enderror">
                                <label for="logo" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>{{ __('dashboard.Choose Logo') }}</span>
                                </label>
                            </div>
                            @error('logo')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                            @if($agentProfile && $agentProfile->logo)
                                <div class="current-logo">
                                    <img src="{{ Storage::url($agentProfile->logo) }}" alt="Current Logo">
                                    <p>{{ __('dashboard.Current Logo') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-field">
                            <label for="address_ar">{{ __('dashboard.Address') }} ({{ __('dashboard.Arabic') }})</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <textarea id="address_ar" name="address_ar" rows="3" 
                                          class="form-textarea @error('address_ar') error @enderror">{{ old('address_ar', is_array($agentProfile->address ?? null) ? ($agentProfile->address['ar'] ?? '') : ($agentProfile->address ?? '')) }}</textarea>
                            </div>
                            @error('address_ar')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-field">
                            <label for="address_en">{{ __('dashboard.Address') }} ({{ __('dashboard.English') }})</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <textarea id="address_en" name="address_en" rows="3" 
                                          class="form-textarea @error('address_en') error @enderror">{{ old('address_en', is_array($agentProfile->address ?? null) ? ($agentProfile->address['en'] ?? '') : ($agentProfile->address ?? '')) }}</textarea>
                            </div>
                            @error('address_en')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Hours Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>{{ __('dashboard.Working Hours') }}</h3>
                </div>
                <div class="card-body">
                    <div class="working-hours-grid">
                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div class="day-schedule {{ $day }}">
                                <div class="day-header">
                                    <i class="fas fa-calendar-day"></i>
                                    <label>{{ __('dashboard.' . ucfirst($day)) }}</label>
                                </div>
                                <div class="time-inputs">
                                    <input type="time" name="working_hours[{{ $day }}][]" 
                                           value="{{ old('working_hours.' . $day . '.0', $agentProfile->working_hours[$day][0] ?? '09:00') }}" 
                                           class="time-input">
                                    <span class="time-separator">{{ __('dashboard.to') }}</span>
                                    <input type="time" name="working_hours[{{ $day }}][]" 
                                           value="{{ old('working_hours.' . $day . '.1', $agentProfile->working_hours[$day][1] ?? '18:00') }}" 
                                           class="time-input">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Specializations Card -->
            <div class="form-card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>{{ __('dashboard.Specializations') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-field">
                        <label>{{ __('dashboard.Select Specializations') }}</label>
                        <div class="checkbox-grid">
                            @php
                                $currentSpecializations = [];
                                if ($agentProfile && $agentProfile->specializations) {
                                    if (is_array($agentProfile->specializations)) {
                                        $currentSpecializations = $agentProfile->specializations['ar'] ?? [];
                                    } else {
                                        $currentSpecializations = [];
                                    }
                                }
                                $oldSpecializations = old('specializations', $currentSpecializations);
                                if (!is_array($oldSpecializations)) {
                                    $oldSpecializations = [];
                                }
                            @endphp
                            @foreach(['washing', 'ironing', 'dry_cleaning', 'pickup_delivery', 'express_service'] as $specialization)
                                <label class="checkbox-label">
                                    <input type="checkbox" name="specializations[]" value="{{ $specialization }}" 
                                           {{ in_array($specialization, $oldSpecializations) ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    <span class="checkbox-text">{{ __('dashboard.' . ucfirst(str_replace('_', ' ', $specialization))) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ __('dashboard.Update Profile') }}
            </button>
            <a href="{{ route('agent.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ __('dashboard.Back to Dashboard') }}
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
/* Hero Section */
.profile-hero {
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

.profile-avatar {
    flex-shrink: 0;
}

.avatar-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(255,255,255,0.2);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.avatar-image:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    border: 4px solid rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: rgba(255,255,255,0.7);
    backdrop-filter: blur(10px);
}

.profile-info {
    flex: 1;
}

.profile-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.profile-subtitle {
    font-size: 1.2rem;
    margin: 0 0 1rem 0;
    opacity: 0.9;
}

.profile-status {
    display: inline-block;
}

/* Profile Form */
.profile-form {
    margin-top: 2rem;
}

.profile-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

/* Form Cards */
.form-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.form-card:hover {
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

/* Form Elements */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-field label {
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.required {
    color: #dc3545;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #666;
    z-index: 2;
    font-size: 1rem;
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error, .form-select.error, .form-textarea.error {
    border-color: #dc3545;
    background: #fff5f5;
}

.error-message {
    color: #dc3545;
    font-size: 0.85rem;
    font-weight: 500;
}

/* File Upload */
.file-upload {
    position: relative;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border: 2px dashed #667eea;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    cursor: pointer;
    transition: all 0.3s ease;
    color: #667eea;
    font-weight: 600;
}

.file-label:hover {
    background: linear-gradient(135deg, #e8f0ff 0%, #d6e7ff 100%);
    border-color: #4facfe;
}

.file-label i {
    font-size: 1.2rem;
}

.current-logo {
    margin-top: 1rem;
    text-align: center;
}

.current-logo img {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid #e9ecef;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.current-logo p {
    margin: 0.5rem 0 0 0;
    color: #666;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Working Hours */
.working-hours-grid {
    display: grid;
    gap: 1rem;
}

.day-schedule {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.day-schedule:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.day-schedule.monday { border-left: 4px solid #ff6b6b; }
.day-schedule.tuesday { border-left: 4px solid #4ecdc4; }
.day-schedule.wednesday { border-left: 4px solid #45b7d1; }
.day-schedule.thursday { border-left: 4px solid #96ceb4; }
.day-schedule.friday { border-left: 4px solid #feca57; }
.day-schedule.saturday { border-left: 4px solid #ff9ff3; }
.day-schedule.sunday { border-left: 4px solid #54a0ff; }

.day-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.day-header i {
    color: #667eea;
    font-size: 1rem;
}

.day-header label {
    font-weight: 600;
    color: #333;
    margin: 0;
    text-transform: none;
    letter-spacing: normal;
}

.time-inputs {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.time-input {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    background: white;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.time-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.time-separator {
    color: #666;
    font-weight: 600;
    font-size: 0.9rem;
}

/* Checkboxes */
.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.checkbox-label:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #ddd;
    border-radius: 6px;
    position: relative;
    transition: all 0.3s ease;
    background: white;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
}

.checkbox-label input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.checkbox-text {
    font-weight: 500;
    color: #333;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 20px;
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

/* Responsive Design */
@media (max-width: 1024px) {
    .profile-form-grid {
        grid-template-columns: 1fr;
    }
    
    .hero-content {
        flex-direction: column;
        text-align: center;
        gap: 1.5rem;
    }
    
    .profile-title {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .hero-content {
        padding: 2rem;
    }
    
    .avatar-image, .avatar-placeholder {
        width: 100px;
        height: 100px;
    }
    
    .profile-title {
        font-size: 1.8rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .checkbox-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
@endpush
