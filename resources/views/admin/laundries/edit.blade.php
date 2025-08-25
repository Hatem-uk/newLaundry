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

        <form method="POST" action="{{ route('admin.laundries.update', $laundry) }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name_ar">اسم المغسلة (عربي)</label>
                    <input type="text" id="name_ar" name="name_ar" class="form-input @error('name_ar') error @enderror" value="{{ old('name_ar', $name_ar) }}">
                    @error('name_ar')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم المغسلة (إنجليزي)</label>
                    <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ old('name_en', $name_en) }}">
                    @error('name_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" class="form-input @error('email') error @enderror" value="{{ old('email', $email) }}" readonly>
                    <small class="form-help">لا يمكن تغيير البريد الإلكتروني</small>
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف</label>
                    <input type="tel" id="phone" name="phone" class="form-input @error('phone') error @enderror" value="{{ old('phone', $phone) }}">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Location Information -->
            <div class="form-section">
                <h3 class="form-section-title">معلومات الموقع</h3>
                
                <div class="form-group">
                    <label for="city_id">المدينة</label>
                    <select id="city_id" name="city_id" class="form-input @error('city_id') error @enderror">
                        <option value="">اختر المدينة</option>
                        @foreach($cities ?? [] as $city)
                            @php
                                $cityName = json_decode($city->getRawOriginal('name'), true);
                                $displayCityName = $cityName && is_array($cityName) ? ($cityName[app()->getLocale()] ?? $cityName['ar'] ?? $cityName['en'] ?? 'City') : $city->name;
                            @endphp
                            <option value="{{ $city->id }}" {{ old('city_id', $city_id) == $city->id ? 'selected' : '' }}>
                                {{ is_string($displayCityName) ? $displayCityName : 'City' }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_ar">العنوان (عربي)</label>
                    <textarea id="address_ar" name="address_ar" class="form-input @error('address_ar') error @enderror" rows="3">{{ old('address_ar', $address_ar) }}</textarea>
                    @error('address_ar')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_en">العنوان (إنجليزي)</label>
                    <textarea id="address_en" name="address_en" class="form-input @error('address_en') error @enderror" rows="3">{{ old('address_en', $address_en) }}</textarea>
                    @error('address_en')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="latitude">خط العرض</label>
                    <input type="number" id="latitude" name="latitude" class="form-input @error('latitude') error @enderror" value="{{ old('latitude', $latitude) }}" step="any" placeholder="مثال: 24.7136">
                    @error('latitude')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="longitude">خط الطول</label>
                    <input type="number" id="longitude" name="longitude" class="form-input @error('longitude') error @enderror" value="{{ old('longitude', $longitude) }}" step="any" placeholder="مثال: 46.6753">
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
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror" rows="4">{{ old('description', $description) }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="delivery_radius">نطاق التوصيل (كم)</label>
                    <input type="number" id="delivery_radius" name="delivery_radius" class="form-input @error('delivery_radius') error @enderror" value="{{ old('delivery_radius', $delivery_radius ?? 10) }}" min="1" max="50">
                    @error('delivery_radius')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">حالة العمل</label>
                    <select id="status" name="status" class="form-input @error('status') error @enderror">
                        <option value="online" {{ old('status', $status) == 'online' ? 'selected' : '' }}>متصل</option>
                        <option value="offline" {{ old('status', $status) == 'offline' ? 'selected' : '' }}>غير متصل</option>
                        <option value="maintenance" {{ old('status', $status) == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">الحالة</label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror">
                        <option value="1" {{ old('is_active', $is_active) == 1 ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active', $is_active) == 0 ? 'selected' : '' }}>غير نشط</option>
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
                    <input type="url" id="website" name="website" class="form-input @error('website') error @enderror" value="{{ old('website', $website) }}" placeholder="https://example.com">
                    @error('website')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="facebook">فيسبوك</label>
                    <input type="url" id="facebook" name="facebook" class="form-input @error('facebook') error @enderror" value="{{ old('facebook', $facebook) }}" placeholder="https://facebook.com/username">
                    @error('facebook')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="instagram">انستغرام</label>
                    <input type="url" id="instagram" name="instagram" class="form-input @error('instagram') error @enderror" value="{{ old('instagram', $instagram) }}" placeholder="https://instagram.com/username">
                    @error('instagram')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="whatsapp">واتساب</label>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-input @error('whatsapp') error @enderror" value="{{ old('whatsapp', $whatsapp) }}" placeholder="+966501234567">
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
                    @if($logo)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $logo) }}" alt="الشعار الحالي" style="max-width: 100px; height: auto; margin-bottom: 10px;">
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
    <script src="{{ asset('js/admin-edit-forms.js') }}"></script>
@endpush
