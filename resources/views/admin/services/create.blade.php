@extends('layouts.admin')

@section('title', 'إضافة خدمة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>إضافة خدمة جديدة</h1>
        <p>إنشاء خدمة جديدة في النظام</p>
    </div>

    <!-- Create Service Form -->
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

        <form method="POST" action="{{ route('admin.services.store') }}" class="create-form" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3>المعلومات الأساسية</h3>
                
                <div class="form-group">
                    <label for="name_ar">اسم الخدمة (عربي) *</label>
                    <input type="text" id="name_ar" name="name_ar" value="{{ is_string(old('name_ar')) ? old('name_ar') : '' }}" required class="form-input">
                    @error('name_ar')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في اسم الخدمة (عربي)' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_en">اسم الخدمة (إنجليزي) *</label>
                    <input type="text" id="name_en" name="name_en" value="{{ is_string(old('name_en')) ? old('name_en') : '' }}" required class="form-input">
                    @error('name_en')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في اسم الخدمة (إنجليزي)' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="type">نوع الخدمة *</label>
                    <select id="type" name="type" required class="form-input">
                        <option value="">اختر النوع</option>
                        <option value="washing" {{ is_string(old('type')) && old('type') == 'washing' ? 'selected' : '' }}>غسيل</option>
                        <option value="ironing" {{ is_string(old('type')) && old('type') == 'ironing' ? 'selected' : '' }}>كوي</option>
                        <option value="dry_cleaning" {{ is_string(old('type')) && old('type') == 'dry_cleaning' ? 'selected' : '' }}>تنظيف جاف</option>
                        <option value="agent_supply" {{ is_string(old('type')) && old('type') == 'agent_supply' ? 'selected' : '' }}>إمداد الوكيل</option>
                        <option value="laundry_service" {{ is_string(old('type')) && old('type') == 'laundry_service' ? 'selected' : '' }}>خدمة مغسلة</option>
                    </select>
                    @error('type')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في نوع الخدمة' }}</span>
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

            <!-- Provider Information -->
            <div class="form-section">
                <h3>معلومات المزود</h3>
                
                <div class="form-group">
                    <label for="provider_type">نوع المزود *</label>
                    <select id="provider_type" name="provider_type" required class="form-input">
                        <option value="">اختر نوع المزود</option>
                        <option value="laundry" {{ is_string(old('provider_type')) && old('provider_type') == 'laundry' ? 'selected' : '' }}>مغسلة</option>
                        <option value="agent" {{ is_string(old('provider_type')) && old('provider_type') == 'agent' ? 'selected' : '' }}>وكيل</option>
                    </select>
                    @error('provider_type')
                        <span class="error-message">{{ is_string($message) ? $message : 'خطأ في نوع المزود' }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="provider_id">المزود *</label>
                    <select id="provider_id" name="provider_id" required class="form-input">
                        <option value="">اختر المزود</option>
                    </select>
                    @error('provider_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="form-section">
                <h3>معلومات التسعير</h3>
                
                <div class="form-group">
                    <label for="coin_cost">التكلفة بالنقاط</label>
                    <input type="number" id="coin_cost" name="coin_cost" value="{{ is_numeric(old('coin_cost')) ? old('coin_cost') : '' }}" min="0" class="form-input">
                    <small class="form-help">عدد النقاط المطلوبة لشراء الخدمة</small>
                    @error('coin_cost')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">السعر بالنقود</label>
                    <input type="number" id="price" name="price" value="{{ is_numeric(old('price')) ? old('price') : '' }}" step="0.01" min="0" class="form-input">
                    <small class="form-help">السعر بالريال السعودي</small>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="quantity">الكمية</label>
                    <input type="number" id="quantity" name="quantity" value="{{ is_numeric(old('quantity')) ? old('quantity') : 1 }}" min="1" class="form-input">
                    <small class="form-help">الكمية الافتراضية للخدمة</small>
                    @error('quantity')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="form-section">
                <h3>معلومات إضافية</h3>
                
                <div class="form-group">
                    <label for="status">الحالة</label>
                    <select id="status" name="status" class="form-input">
                        <option value="pending" {{ (is_string(old('status')) && old('status') == 'pending') || old('status') === null ? 'selected' : '' }}>في الانتظار</option>
                        <option value="active" {{ is_string(old('status')) && old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="approved" {{ is_string(old('status')) && old('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                        <option value="rejected" {{ is_string(old('status')) && old('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="inactive" {{ is_string(old('status')) && old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">صورة الخدمة</label>
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
                    إنشاء الخدمة
                </button>
                <a href="{{ route('admin.services') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const providerTypeSelect = document.getElementById('provider_type');
        const providerIdSelect = document.getElementById('provider_id');
        
        // Provider type change handler
        providerTypeSelect.addEventListener('change', function() {
            const providerType = this.value;
            providerIdSelect.innerHTML = '<option value="">اختر المزود</option>';
            
            if (providerType === 'laundry') {
                // Add laundry options
                @foreach($laundries as $laundry)
                    const option = document.createElement('option');
                    option.value = '{{ $laundry->user_id }}';
                    option.textContent = '{{ $laundry->user->name ?? "مغسلة" }}';
                    providerIdSelect.appendChild(option);
                @endforeach
            } else if (providerType === 'agent') {
                // Add agent options
                @foreach($agents as $agent)
                    const option = document.createElement('option');
                    option.value = '{{ $agent->user_id }}';
                    option.textContent = '{{ $agent->user->name ?? "وكيل" }}';
                    providerIdSelect.appendChild(option);
                @endforeach
            }
        });
    });
</script>
@endpush
