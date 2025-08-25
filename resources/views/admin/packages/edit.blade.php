@extends('layouts.admin')

@section('title', 'تعديل الطرد')

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>تعديل الطرد</h1>
        <p>{{ json_decode($package->getRawOriginal('name'), true)[app()->getLocale()] ?? $package->name }}</p>
    </div>

    <!-- Edit Package Form -->
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

        <form method="POST" action="{{ route('admin.packages.update', $package) }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name">اسم الطرد (عربي) *</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ json_decode($package->getRawOriginal('name'), true)['ar'] ?? old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم الطرد (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ json_decode($package->getRawOriginal('name'), true)['en'] ?? old('name_en') }}" required>
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">نوع الطرد *</label>
                    <select id="type" name="type" class="form-input @error('type') error @enderror" required>
                        <option value="">{{ __('dashboard.select_type') }}</option>
                        <option value="gift" {{ old('type', $package->type) == 'gift' ? 'selected' : '' }}>هدية</option>
                        <option value="purchase" {{ old('type', $package->type) == 'purchase' ? 'selected' : '' }}>شراء</option>
                        <option value="bonus" {{ old('type', $package->type) == 'bonus' ? 'selected' : '' }}>مكافأة</option>
                    </select>
                    @error('type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-section">
                <h3 class="form-section-title">الوصف</h3>
                
                <div class="form-group">
                    <label for="description">الوصف (عربي)</label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror" rows="3">{{ json_decode($package->getRawOriginal('description'), true)['ar'] ?? old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description_en">الوصف (إنجليزي)</label>
                    <textarea id="description_en" name="description_en" class="form-input @error('description_en') error @enderror" rows="3">{{ json_decode($package->getRawOriginal('description'), true)['en'] ?? old('description_en') }}</textarea>
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
                    <input type="number" id="price" name="price" class="form-input @error('price') error @enderror" value="{{ $package->price ?? old('price') }}" step="0.01" min="0" required>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="coins">العملات</label>
                    <input type="number" id="coins" name="coins" class="form-input @error('coins') error @enderror" value="{{ $package->coins ?? old('coins') }}" min="0">
                    <small class="form-help">عدد العملات المطلوبة لشراء هذا الطرد</small>
                    @error('coins')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">الحالة *</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ old('is_active', $package->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active', $package->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('is_active')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Package Details -->
            <div class="form-section">
                <h3 class="form-section-title">تفاصيل الطرد</h3>
                
                <div class="form-group">
                    <label for="quantity">الكمية المتاحة</label>
                    <input type="number" id="quantity" name="quantity" class="form-input @error('quantity') error @enderror" value="{{ $package->quantity ?? old('quantity') }}" min="0">
                    @error('quantity')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="expiry_date">تاريخ انتهاء الصلاحية</label>
                    <input type="date" id="expiry_date" name="expiry_date" class="form-input @error('expiry_date') error @enderror" value="{{ old('expiry_date', $package->expiry_date ? $package->expiry_date->format('Y-m-d') : '') }}">
                    @error('expiry_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">صورة الطرد</label>
                    @if($package->image)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $package->image) }}" alt="صورة الطرد الحالية" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                            <p>الصورة الحالية</p>
                        </div>
                    @endif
                    <input type="file" id="image" name="image" class="form-input" accept="image/*">
                    <small class="form-help">الصيغ المسموحة: JPEG, PNG, JPG, GIF (الحد الأقصى: 2MB)</small>
                    @error('image')
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
                <a href="{{ route('admin.packages.view', $package) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    عرض الطرد
                </a>
                <a href="{{ route('admin.packages') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    العودة للطرود
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
