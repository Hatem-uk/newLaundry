@extends('layouts.admin')

@section('title', 'إضافة مغسلة جديدة')

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>إضافة مغسلة جديدة</h1>
    </div>

    <!-- Create Laundry Form -->
    <div class="section-container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>أخطاء في التحقق:</strong>
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.laundries.store') }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Basic Information -->
            <div class="form-section">
                <h3>المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name">اسم المغسلة (عربي) *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-input">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم المغسلة (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" value="{{ old('name_en') }}" required class="form-input">
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-input">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required class="form-input">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور *</label>
                    <input type="password" id="password" name="password" required class="form-input">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">تأكيد كلمة المرور *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="form-input">
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3>معلومات الموقع</h3>
                
                <div class="form-group">
                    <label for="city_id">المدينة *</label>
                    <select id="city_id" name="city_id" required class="form-input">
                        <option value="">اختر المدينة</option>
                        @foreach($cities ?? [] as $city)
                            @php
                                $cityName = json_decode($city->getRawOriginal('name'), true);
                                $displayCityName = $cityName && is_array($cityName) ? ($cityName[app()->getLocale()] ?? $cityName['ar'] ?? $cityName['en'] ?? 'City') : $city->name;
                            @endphp
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ is_string($displayCityName) ? $displayCityName : 'City' }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">العنوان التفصيلي (عربي) *</label>
                    <textarea id="address" name="address" required class="form-input" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_en">العنوان التفصيلي (إنجليزي) *</label>
                    <textarea id="address_en" name="address_en" required class="form-input" rows="3">{{ old('address_en') }}</textarea>
                    @error('address_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="latitude">خط العرض</label>
                    <input type="number" id="latitude" name="latitude" value="{{ old('latitude') }}" step="any" class="form-input">
                </div>

                <div class="form-group">
                    <label for="longitude">خط الطول</label>
                    <input type="number" id="longitude" name="longitude" value="{{ old('longitude') }}" step="any" class="form-input">
                </div>
            </div>

            <!-- Business Information -->
            <div class="form-section">
                <h3>معلومات العمل</h3>
                
                <div class="form-group">
                    <label for="description">وصف المغسلة</label>
                    <textarea id="description" name="description" class="form-input" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="working_hours">ساعات العمل</label>
                    <input type="text" id="working_hours" name="working_hours" value="{{ old('working_hours') }}" placeholder="مثال: 8:00 ص - 8:00 م" class="form-input">
                </div>

                <div class="form-group">
                    <label for="delivery_radius">نطاق التوصيل (كم)</label>
                    <input type="number" id="delivery_radius" name="delivery_radius" value="{{ old('delivery_radius', 10) }}" min="1" max="50" class="form-input">
                </div>

                <div class="form-group">
                    <label for="logo">شعار المغسلة</label>
                    <input type="file" id="logo" name="logo" class="form-input" accept="image/*">
                    <small class="form-help">الصيغ المسموحة: JPEG, PNG, JPG, GIF (الحد الأقصى: 2MB)</small>
                </div>

                <div class="form-group">
                    <label for="is_active">الحالة</label>
                    <select id="is_active" name="is_active" class="form-input">
                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3>معلومات التواصل</h3>
                
                <div class="form-group">
                    <label for="website">الموقع الإلكتروني</label>
                    <input type="url" id="website" name="website" value="{{ old('website') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="facebook">صفحة فيسبوك</label>
                    <input type="url" id="facebook" name="facebook" value="{{ old('facebook') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="instagram">حساب انستغرام</label>
                    <input type="url" id="instagram" name="instagram" value="{{ old('instagram') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="whatsapp">رقم الواتساب</label>
                    <input type="tel" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" class="form-input">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    إنشاء المغسلة
                </button>
                                 <a href="{{ route('admin.laundries') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-edit-forms.css') }}">
    <style>
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        .form-input.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid transparent;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .alert ul {
            margin: 0;
            padding-right: 20px;
        }

        .alert li {
            margin-bottom: 5px;
        }

        .alert i {
            margin-right: 8px;
            font-size: 16px;
        }

        .alert strong {
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
<script>
    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.edit-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');

        // Password confirmation validation
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('كلمات المرور غير متطابقة');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }

        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);

        // Form submission
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                // Show validation messages
                form.reportValidity();
            }
        });
    });
</script>
    <script src="{{ asset('js/admin-edit-forms.js') }}"></script>
@endpush
