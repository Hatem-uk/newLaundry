@extends('layouts.admin')

@section('title', 'تعديل المدينة')

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>تعديل المدينة</h1>
        <p>{{ json_decode($city->getRawOriginal('name'), true)[app()->getLocale()] ?? $city->name }}</p>
    </div>

    <!-- Edit City Form -->
    <div class="section-container">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="edit-form">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name">اسم المدينة (عربي) *</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ json_decode($city->getRawOriginal('name'), true)['ar'] ?? old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم المدينة (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ json_decode($city->getRawOriginal('name'), true)['en'] ?? old('name_en') }}" required>
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3 class="form-section-title">معلومات الموقع</h3>
                
                <div class="form-group">
                    <label for="latitude">خط العرض</label>
                    <input type="number" id="latitude" name="latitude" class="form-input @error('latitude') error @enderror" value="{{ $city->latitude ?? old('latitude') }}" step="any" placeholder="مثال: 24.7136">
                    @error('latitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="longitude">خط الطول</label>
                    <input type="number" id="longitude" name="longitude" class="form-input @error('longitude') error @enderror" value="{{ $city->longitude ?? old('longitude') }}" step="any" placeholder="مثال: 46.6753">
                    @error('longitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="timezone">المنطقة الزمنية</label>
                    <select id="timezone" name="timezone" class="form-input @error('timezone') error @enderror">
                        <option value="">{{ __('dashboard.select_timezone') }}</option>
                        <option value="Asia/Riyadh" {{ old('timezone', $city->timezone) == 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh (GMT+3)</option>
                        <option value="Asia/Dubai" {{ old('timezone', $city->timezone) == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GMT+4)</option>
                        <option value="Asia/Kuwait" {{ old('timezone', $city->timezone) == 'Asia/Kuwait' ? 'selected' : '' }}>Asia/Kuwait (GMT+3)</option>
                        <option value="Asia/Qatar" {{ old('timezone', $city->timezone) == 'Asia/Qatar' ? 'selected' : '' }}>Asia/Qatar (GMT+3)</option>
                        <option value="Asia/Bahrain" {{ old('timezone', $city->timezone) == 'Asia/Bahrain' ? 'selected' : '' }}>Asia/Bahrain (GMT+3)</option>
                        <option value="Asia/Muscat" {{ old('timezone', $city->timezone) == 'Asia/Muscat' ? 'selected' : '' }}>Asia/Muscat (GMT+4)</option>
                    </select>
                    @error('timezone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Status and Settings -->
            <div class="form-section">
                <h3 class="form-section-title">الحالة والإعدادات</h3>
                
                <div class="form-group">
                    <label for="is_active">الحالة *</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ old('is_active', $city->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active', $city->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('is_active')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sort_order">ترتيب العرض</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-input @error('sort_order') error @enderror" value="{{ $city->sort_order ?? old('sort_order', 0) }}" min="0">
                    <small class="form-help">الترتيب في قائمة المدن (الأقل = الأعلى)</small>
                    @error('sort_order')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    حفظ التغييرات
                </button>
                <a href="{{ route('admin.cities.view', $city) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    عرض المدينة
                </a>
                <a href="{{ route('admin.cities') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    العودة للمدن
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-edit-forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-edit-forms.js') }}"></script>
@endpush
