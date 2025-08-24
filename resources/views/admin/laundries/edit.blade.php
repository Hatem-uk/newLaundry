@extends('layouts.admin')

@section('title', __('dashboard.edit_laundry') . ' - ' . (($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>{{ __('dashboard.edit_laundry') }}</h1>
            <p>{{ ($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.laundries.view', $laundry) }}" class="back-btn">
                <i class="fas fa-eye"></i>
                {{ __('dashboard.view_details') }}
            </a>
        </div>
    </div>

    <!-- Edit Laundry Form -->
    <div class="section-container">
        <form method="POST" action="{{ route('admin.laundries.update', $laundry) }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    <i class="fas fa-check-circle"></i>
                    <strong>{{ __('dashboard.success') }}!</strong>
                    {{ session('success') }}
                    <button type="button" class="alert-close" onclick="closeAlert('success-alert')">×</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" id="validation-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>{{ __('dashboard.validation_errors') }}:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="alert-close" onclick="closeAlert('validation-alert')">×</button>
                </div>
            @endif

            <!-- Basic Information -->
            <div class="form-section">
                <h3>{{ __('dashboard.basic_information') }}</h3>
                
                <div class="form-group">
                    <label for="name">{{ __('dashboard.laundry_name') }} ({{ __('dashboard.arabic') }})</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ ($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData['ar'] ?? old('name')) : old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">{{ __('dashboard.laundry_name') }} ({{ __('dashboard.english') }})</label>
                    <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ ($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData['en'] ?? old('name_en')) : old('name_en') }}" required>
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ __('dashboard.email') }}</label>
                    <input type="email" id="email" name="email" class="form-input @error('email') error @enderror" value="{{ old('email', $laundry->user->email) }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">{{ __('dashboard.phone') }}</label>
                    <input type="tel" id="phone" name="phone" class="form-input @error('phone') error @enderror" value="{{ old('phone', $laundry->phone) }}" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3>{{ __('dashboard.location_information') }}</h3>
                
                <div class="form-group">
                    <label for="city_id">{{ __('dashboard.city') }}</label>
                    <select id="city_id" name="city_id" class="form-input @error('city_id') error @enderror" required>
                        <option value="">{{ __('dashboard.select_city') }}</option>
                        @foreach($cities ?? [] as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $laundry->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->getTranslation('name', app()->getLocale()) ?? $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">{{ __('dashboard.address') }} ({{ __('dashboard.arabic') }})</label>
                    <textarea id="address" name="address" class="form-input @error('address') error @enderror" rows="3" required>{{ ($addressData = json_decode($laundry->getRawOriginal('address'), true)) ? ($addressData['ar'] ?? old('address')) : old('address') }}</textarea>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_en">{{ __('dashboard.address') }} ({{ __('dashboard.english') }})</label>
                    <textarea id="address_en" name="address_en" class="form-input @error('address_en') error @enderror" rows="3" required>{{ ($addressData = json_decode($laundry->getRawOriginal('address'), true)) ? ($addressData['en'] ?? old('address_en')) : old('address_en') }}</textarea>
                    @error('address_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="latitude">{{ __('dashboard.latitude') }}</label>
                    <input type="number" id="latitude" name="latitude" class="form-input @error('latitude') error @enderror" value="{{ old('latitude', $laundry->latitude) }}" step="any">
                    @error('latitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="longitude">{{ __('dashboard.longitude') }}</label>
                    <input type="number" id="longitude" name="longitude" class="form-input @error('longitude') error @enderror" value="{{ old('longitude', $laundry->longitude) }}" step="any">
                    @error('longitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Business Information -->
            <div class="form-section">
                <h3>{{ __('dashboard.business_information') }}</h3>
                
                <div class="form-group">
                    <label for="description">{{ __('dashboard.description') }}</label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror" rows="4">{{ old('description', is_string($laundry->description) ? $laundry->description : '') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="working_hours">{{ __('dashboard.working_hours') }}</label>
                    <input type="text" id="working_hours" name="working_hours" class="form-input @error('working_hours') error @enderror" value="{{ old('working_hours', is_string($laundry->working_hours) ? $laundry->working_hours : '') }}" placeholder="{{ __('dashboard.working_hours_placeholder') }}">
                    @error('working_hours')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_radius">{{ __('dashboard.delivery_radius') }} ({{ __('dashboard.km') }})</label>
                    <input type="number" id="delivery_radius" name="delivery_radius" class="form-input @error('delivery_radius') error @enderror" value="{{ old('delivery_radius', $laundry->delivery_radius ?? 10) }}" min="1" max="50">
                    @error('delivery_radius')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">{{ __('dashboard.status') }}</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror">
                        <option value="1" {{ old('is_active', $laundry->is_active) == 1 ? 'selected' : '' }}>{{ __('dashboard.active') }}</option>
                        <option value="0" {{ old('is_active', $laundry->is_active) == 0 ? 'selected' : '' }}>{{ __('dashboard.inactive') }}</option>
                    </select>
                    @error('is_active')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3>{{ __('dashboard.contact_information') }}</h3>
                
                <div class="form-group">
                    <label for="website">{{ __('dashboard.website') }}</label>
                    <input type="url" id="website" name="website" class="form-input @error('website') error @enderror" value="{{ old('website', $laundry->website) }}">
                    @error('website')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="facebook">{{ __('dashboard.facebook') }}</label>
                    <input type="url" id="facebook" name="facebook" class="form-input @error('facebook') error @enderror" value="{{ old('facebook', $laundry->facebook) }}">
                    @error('facebook')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="instagram">{{ __('dashboard.instagram') }}</label>
                    <input type="url" id="instagram" name="instagram" class="form-input @error('instagram') error @enderror" value="{{ old('instagram', $laundry->instagram) }}">
                    @error('instagram')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="whatsapp">{{ __('dashboard.whatsapp') }}</label>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-input @error('whatsapp') error @enderror" value="{{ old('whatsapp', $laundry->whatsapp) }}">
                    @error('whatsapp')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('dashboard.save_changes') }}
                </button>
                <a href="{{ route('admin.laundries.view', $laundry) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    {{ __('dashboard.view_details') }}
                </a>
                <a href="{{ route('admin.laundries') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('dashboard.back_to_list') }}
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-content h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
    }

    .header-content p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .back-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        animation: slideIn 0.3s ease;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert i {
        font-size: 18px;
        flex-shrink: 0;
    }

    .alert strong {
        font-weight: 600;
    }

    .alert-close {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .alert-close:hover {
        opacity: 1;
    }

    .section-container {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .form-section h3 {
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control:invalid {
        border-color: #dc3545;
    }

    .form-help {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #6c757d;
        font-style: italic;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-start;
        align-items: center;
        flex-wrap: wrap;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5a6fd8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .btn-outline {
        background: transparent;
        color: #6c757d;
        border: 2px solid #6c757d;
    }

    .btn-outline:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .header-content h1 {
            font-size: 2rem;
        }

        .section-container {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .btn {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.edit-form');
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
            }
        });

        // Auto-save draft functionality
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                // Save form data to localStorage as draft
                const formData = new FormData(form);
                const draftData = {};
                for (let [key, value] of formData.entries()) {
                    draftData[key] = value;
                }
                localStorage.setItem('laundry_edit_draft', JSON.stringify(draftData));
            });
        });

        // Load draft data if available
        const draftData = localStorage.getItem('laundry_edit_draft');
        if (draftData) {
            try {
                const draft = JSON.parse(draftData);
                Object.keys(draft).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input && !input.value) {
                        input.value = draft[key];
                    }
                });
            } catch (e) {
                console.error('Error loading draft data:', e);
            }
        }

        // Clear draft on successful submission
        form.addEventListener('submit', function() {
            localStorage.removeItem('laundry_edit_draft');
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        });
    }, 5000);

    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }
    }
</script>
@endpush
