<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تعديل الوكيل - {{ $agent->user->name }} - موج</title>
    <link rel="stylesheet" href="{{ asset('dashboard/styles.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .current-logo {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .current-logo img {
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .current-logo p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        
        .form-help {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        
        input[type="file"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-water"></i>
                <span>موج</span>
            </div>
            
            <nav class="nav-menu">
                <a href="{{ route('admin.dashboard') }}" class="nav-item" data-page="home">
                    <i class="fas fa-th-large"></i>
                    <span>الرئيسية</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item" data-page="users">
                    <i class="fas fa-users"></i>
                    <span>ادارة المستخدمين</span>
                </a>
                <a href="{{ route('admin.agents') }}" class="nav-item active" data-page="agents">
                    <i class="fas fa-user-tie"></i>
                    <span>ادارة الوكلاء</span>
                </a>
                <a href="{{ route('admin.laundries') }}" class="nav-item" data-page="laundries">
                    <i class="fas fa-tshirt"></i>
                    <span>ادارة المغاسل</span>
                </a>
                <a href="{{ route('admin.services') }}" class="nav-item" data-page="services">
                    <i class="fas fa-file-alt"></i>
                    <span>ادارة الخدمات</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item" data-page="orders">
                    <i class="fas fa-list"></i>
                    <span>الطلبات</span>
                </a>

                <a href="{{ route('admin.profile.show') }}" class="nav-item">
                    <i class="fas fa-user-cog"></i>
                    <span>الملف الشخصي</span>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-item logout" style="background: none; border: none; width: 100%; text-align: right; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>تسجيل الخروج</span>
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="admin-info">
                    <div class="admin-details">
                        <span class="admin-title">Admin</span>
                        <span class="admin-email">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="admin-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </header>

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

                    <div class="form-group">
                        <label for="logo">الشعار</label>
                        @if($agent->logo)
                            <div class="current-logo">
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

                    <div class="form-group">
                        <label for="status">الحالة *</label>
                        <select id="status" name="status" required class="form-input">
                            <option value="pending" {{ old('status', $agent->user->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="approved" {{ old('status', $agent->user->status) == 'approved' ? 'selected' : '' }}>نشط</option>
                            <option value="rejected" {{ old('status', $agent->user->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
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

                    <div class="form-group">
                        <label for="password">كلمة المرور الجديدة (اتركها فارغة إذا لم ترد تغييرها)</label>
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

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.agents.view', $agent) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i>
                            عرض الوكيل
                        </a>
                        <a href="{{ route('admin.agents') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            العودة للقائمة
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="{{ asset('dashboard/script.js') }}"></script>
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation item
            const currentPage = 'agents';
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.page === currentPage) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
