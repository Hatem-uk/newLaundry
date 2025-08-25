@extends('layouts.admin')

@section('title', 'تعديل الوكيل - ' . $agent->user->name)

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>تعديل الوكيل</h1>
        <p>{{ $agent->user->name }}</p>
    </div>

    <!-- Edit Form -->
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

        <form method="POST" action="{{ route('admin.agents.update', $agent) }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name">الاسم *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $agent->user->name) }}" required class="form-input">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $agent->user->email) }}" required class="form-input">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">رقم الهاتف</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $agent->phone) }}" class="form-input">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="license_number">رقم الترخيص *</label>
                    <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $agent->license_number) }}" required class="form-input">
                    @error('license_number')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city_id">المدينة *</label>
                    <select id="city_id" name="city_id" required class="form-input">
                        <option value="">اختر المدينة</option>
                        @foreach(\App\Models\City::all() as $city)
                            <option value="{{ $city->id }}" {{ old('city_id', $agent->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">العنوان</label>
                    <textarea id="address" name="address" class="form-input" rows="3">{{ old('address', $agent->address) }}</textarea>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Status and Settings -->
            <div class="form-section">
                <h3 class="form-section-title">الحالة والإعدادات</h3>
                
                <div class="form-group">
                    <label for="status">الحالة *</label>
                    <select id="status" name="status" required class="form-input">
                        <option value="pending" {{ old('status', $agent->user->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                        <option value="approved" {{ old('status', $agent->user->status) == 'approved' ? 'selected' : '' }}>نشط</option>
                        <option value="rejected" {{ old('status', $agent->user->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="suspended" {{ old('status', $agent->user->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">حالة الوكيل</label>
                    <select id="is_active" name="is_active" class="form-input">
                        <option value="1" {{ old('is_active', $agent->is_active) ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ old('is_active', $agent->is_active) ? '' : 'selected' }}>غير نشط</option>
                    </select>
                    @error('is_active')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Logo Upload Section -->
            <div class="form-section">
                <h3 class="form-section-title">الشعار</h3>
                
                <div class="form-group">
                    <label for="logo">الشعار</label>
                    @if($agent->logo)
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $agent->logo) }}" alt="الشعار الحالي" style="max-width: 100px; height: auto; margin-bottom: 10px;">
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
                <a href="{{ route('admin.agents.view', $agent) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    عرض الوكيل
                </a>
                <a href="{{ route('admin.agents') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-right"></i>
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
