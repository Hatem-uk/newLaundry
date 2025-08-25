@extends('layouts.admin')

@section('title', 'تعديل المغسلة')

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>تعديل المغسلة</h1>
        <p>{{ json_decode($laundry->getRawOriginal('name'), true)[app()->getLocale()] ?? $laundry->name }}</p>
    </div>

    <!-- Edit Laundry Form -->
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

        <form method="POST" action="{{ route('admin.laundries.update', $laundry) }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name">اسم المغسلة (عربي) *</label>
                    <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ json_decode($laundry->getRawOriginal('name'), true)['ar'] ?? old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم المغسلة (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ json_decode($laundry->getRawOriginal('name'), true)['en'] ?? old('name_en') }}" required>
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني *</label>
                    <input type="email" id="email" name="email" class="form-input @error('email') error @enderror" value="{{ old('email', $laundry->user->email) }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف *</label>
                    <input type="tel" id="phone" name="phone" class="form-input @error('phone') error @enderror" value="{{ old('phone', $laundry->phone) }}" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3 class="form-section-title">معلومات الموقع</h3>
                
                <div class="form-group">
                    <label for="city_id">المدينة *</label>
                    <select id="city_id" name="city_id" class="form-input @error('city_id') error @enderror" required>
                        <option value="">اختر المدينة</option>
                        @foreach($cities ?? [] as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $laundry->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">العنوان (عربي) *</label>
                    <textarea id="address" name="address" class="form-input @error('address') error @enderror" rows="3" required>{{ json_decode($laundry->getRawOriginal('address'), true)['ar'] ?? old('address') }}</textarea>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_en">العنوان (إنجليزي) *</label>
                    <textarea id="address_en" name="address_en" class="form-input @error('address_en') error @enderror" rows="3" required>{{ json_decode($laundry->getRawOriginal('address'), true)['en'] ?? old('address_en') }}</textarea>
                    @error('address_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="latitude">خط العرض</label>
                    <input type="number" id="latitude" name="latitude" class="form-input @error('latitude') error @enderror" value="{{ old('latitude', $laundry->latitude) }}" step="any" placeholder="مثال: 24.7136">
                    @error('latitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="longitude">خط الطول</label>
                    <input type="number" id="longitude" name="longitude" class="form-input @error('longitude') error @enderror" value="{{ old('longitude', $laundry->longitude) }}" step="any" placeholder="مثال: 46.6753">
                    @error('longitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Business Information -->
            <div class="form-section">
                <h3 class="form-section-title">معلومات العمل</h3>
                
                <div class="form-group">
                    <label for="description">الوصف</label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror" rows="4">{{ old('description', is_string($laundry->description) ? $laundry->description : '') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="working_hours">ساعات العمل</label>
                    <input type="text" id="working_hours" name="working_hours" class="form-input @error('working_hours') error @enderror" value="{{ old('working_hours', is_string($laundry->working_hours) ? $laundry->working_hours : '') }}" placeholder="مثال: 8:00 ص - 10:00 م">
                    @error('working_hours')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="delivery_radius">نطاق التوصيل (كم) *</label>
                    <input type="number" id="delivery_radius" name="delivery_radius" class="form-input @error('delivery_radius') error @enderror" value="{{ old('delivery_radius', $laundry->delivery_radius ?? 10) }}" min="1" max="50" required>
                    @error('delivery_radius')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">الحالة *</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ old('is_active', $laundry->is_active) == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active', $laundry->is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('is_active')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3 class="form-section-title">معلومات الاتصال</h3>
                
                <div class="form-group">
                    <label for="website">الموقع الإلكتروني</label>
                    <input type="url" id="website" name="website" class="form-input @error('website') error @enderror" value="{{ old('website', $laundry->website) }}" placeholder="https://example.com">
                    @error('website')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="facebook">فيسبوك</label>
                    <input type="url" id="facebook" name="facebook" class="form-input @error('facebook') error @enderror" value="{{ old('facebook', $laundry->facebook) }}" placeholder="https://facebook.com/username">
                    @error('facebook')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="instagram">انستغرام</label>
                    <input type="url" id="instagram" name="instagram" class="form-input @error('instagram') error @enderror" value="{{ old('instagram', $laundry->instagram) }}" placeholder="https://instagram.com/username">
                    @error('instagram')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="whatsapp">واتساب</label>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-input @error('whatsapp') error @enderror" value="{{ old('whatsapp', $laundry->whatsapp) }}" placeholder="+966501234567">
                    @error('whatsapp')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Logo Upload Section -->
            <div class="form-section">
                <h3 class="form-section-title">الشعار</h3>
                
                <div class="form-group">
                    <label for="logo">شعار المغسلة</label>
                    @if($laundry->logo)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $laundry->logo) }}" alt="الشعار الحالي" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                            <p>الشعار الحالي</p>
                        </div>
                    @endif
                    <input type="file" id="logo" name="logo" class="form-input" accept="image/*">
                    <small class="form-help">الصيغ المسموحة: JPEG, PNG, JPG, GIF (الحد الأقصى: 2MB)</small>
                    @error('logo')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password Section -->
            <div class="form-section">
                <h3 class="form-section-title">كلمة المرور</h3>
                <p class="form-help">اتركها فارغة إذا لم ترد تغييرها</p>
                
                <div class="form-group">
                    <label for="password">كلمة المرور الجديدة</label>
                    <input type="password" id="password" name="password" class="form-input">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input">
                    @error('password_confirmation')
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
                <a href="{{ route('admin.laundries.view', $laundry) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    عرض المغسلة
                </a>
                <a href="{{ route('admin.laundries') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    العودة للقائمة
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
