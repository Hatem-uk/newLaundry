@extends('layouts.admin')

@section('title', 'تعديل الخدمة')

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>تعديل الخدمة</h1>
    </div>

    <!-- Edit Service Form -->
    <div class="section-container">
        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="edit-form">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name">اسم الخدمة (عربي) *</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ json_decode($service->getRawOriginal('name'), true)['ar'] ?? old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم الخدمة (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ json_decode($service->getRawOriginal('name'), true)['en'] ?? old('name_en') }}" required>
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-section">
                <h3 class="form-section-title">الوصف</h3>
                
                <div class="form-group">
                    <label for="description">الوصف (عربي)</label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror" rows="3">{{ json_decode($service->getRawOriginal('description'), true)['ar'] ?? old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description_en">الوصف (إنجليزي)</label>
                    <textarea id="description_en" name="description_en" class="form-input @error('description_en') error @enderror" rows="3">{{ json_decode($service->getRawOriginal('description'), true)['en'] ?? old('description_en') }}</textarea>
                    @error('description_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Pricing and Status -->
            <div class="form-section">
                <h3 class="form-section-title">السعر والحالة</h3>
                
                <div class="form-group">
                    <label for="price">السعر *</label>
                    <input type="number" id="price" name="price" class="form-input @error('price') error @enderror" value="{{ $service->price ?? old('price') }}" step="0.01" min="0" required>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="coins">العملات</label>
                    <input type="number" id="coins" name="coins" class="form-input @error('coins') error @enderror" value="{{ $service->coins ?? old('coins') }}" min="0">
                    <small class="form-help">عدد العملات المطلوبة لشراء هذه الخدمة</small>
                    @error('coins')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">الحالة *</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ ($service->is_active == 1) ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ ($service->is_active == 0) ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('is_active')
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
                <a href="{{ route('admin.services.view', $service) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    عرض الخدمة
                </a>
                <a href="{{ route('admin.services') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    العودة للخدمات
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
