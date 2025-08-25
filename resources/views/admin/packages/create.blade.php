@extends('layouts.admin')

@section('title', 'إضافة باقة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>إضافة باقة جديدة</h1>
        <p>إنشاء باقة نقاط جديدة</p>
    </div>

    <!-- Create Package Form -->
    <div class="section-container">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ is_string(session('success')) ? session('success') : 'تمت العملية بنجاح' }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                {{ is_string(session('error')) ? session('error') : 'حدث خطأ في العملية' }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>أخطاء في التحقق:</strong>
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ is_string($error) ? $error : 'خطأ غير محدد' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.packages.store') }}" class="create-form">
            @csrf
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3>المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name_ar">اسم الباقة (عربي) *</label>
                    <input type="text" id="name_ar" name="name_ar" value="{{ is_string(old('name_ar')) ? old('name_ar') : '' }}" required class="form-input">
                    @error('name_ar')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في اسم الباقة (عربي)' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم الباقة (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" value="{{ is_string(old('name_en')) ? old('name_en') : '' }}" required class="form-input">
                    @error('name_en')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في اسم الباقة (إنجليزي)' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">نوع الباقة *</label>
                    <select id="type" name="type" required class="form-input">
                        <option value="">اختر النوع</option>
                        <option value="starter" {{ is_string(old('type')) && old('type') == 'starter' ? 'selected' : '' }}>مبتدئ</option>
                        <option value="premium" {{ is_string(old('type')) && old('type') == 'premium' ? 'selected' : '' }}>مميز</option>
                        <option value="bulk" {{ is_string(old('type')) && old('type') == 'bulk' ? 'selected' : '' }}>كمي</option>
                        <option value="special" {{ is_string(old('type')) && old('type') == 'special' ? 'selected' : '' }}>خاص</option>
                    </select>
                    @error('type')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في نوع الباقة' }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-section">
                <h3>الوصف</h3>
                
                <div class="form-group">
                    <label for="description_ar">الوصف (عربي)</label>
                    <textarea id="description_ar" name="description_ar" class="form-input" rows="3">{{ is_string(old('description_ar')) ? old('description_ar') : '' }}</textarea>
                    @error('description_ar')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في الوصف (عربي)' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description_en">الوصف (إنجليزي)</label>
                    <textarea id="description_en" name="description_en" class="form-input" rows="3">{{ is_string(old('description_en')) ? old('description_en') : '' }}</textarea>
                    @error('description_en')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في الوصف (إنجليزي)' }}</span>
                    @enderror
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="form-section">
                <h3>معلومات التسعير</h3>
                
                <div class="form-group">
                    <label for="price">السعر *</label>
                    <input type="number" id="price" name="price" value="{{ is_numeric(old('price')) ? old('price') : '' }}" min="0" step="0.01" required class="form-input">
                    <small class="form-help">السعر بالريال السعودي</small>
                    @error('price')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في السعر' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="coins_amount">عدد النقاط *</label>
                    <input type="number" id="coins_amount" name="coins_amount" value="{{ is_numeric(old('coins_amount')) ? old('coins_amount') : '' }}" min="1" required class="form-input">
                    <small class="form-help">عدد النقاط التي سيحصل عليها العميل</small>
                    @error('coins_amount')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في عدد النقاط' }}</span>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div class="form-section">
                <h3>الحالة</h3>
                
                <div class="form-group">
                    <label for="status">حالة الباقة</label>
                    <select id="status" name="status" class="form-input">
                        <option value="active" {{ (is_string(old('status')) && old('status') == 'active') || old('status') === null ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ is_string(old('status')) && old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في حالة الباقة' }}</span>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    إنشاء الباقة
                </button>
                <a href="{{ route('admin.packages') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    العودة للقائمة
                </a>
            </div>
        </form>
    </div>
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
