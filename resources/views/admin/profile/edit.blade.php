<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تعديل الملف الشخصي - موج</title>
    <link rel="stylesheet" href="{{ asset('dashboard/styles.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                <a href="{{ route('admin.agents') }}" class="nav-item" data-page="agents">
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

                <a href="{{ route('admin.profile.show') }}" class="nav-item active">
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
                    <div class="header-actions">
                        <!-- Language Switcher -->
                        <div class="language-switcher">
                            <a href="{{ route('language.switch', 'ar') }}" class="lang-btn {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                <i class="fas fa-globe"></i>
                                العربية
                            </a>
                            <a href="{{ route('language.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                <i class="fas fa-globe"></i>
                                English
                            </a>
                        </div>
                        <div class="admin-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Title -->
            <div class="page-header">
                <h1>تعديل الملف الشخصي</h1>
            </div>

            <!-- Edit Profile Form -->
            <div class="section-container">
                <form method="POST" action="{{ route('admin.profile.update') }}" class="edit-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="name">الاسم الكامل</label>
                        <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" class="form-input @error('email') error @enderror" value="{{ old('email', $admin->email) }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">رقم الهاتف</label>
                        <input type="tel" id="phone" name="phone" class="form-input @error('phone') error @enderror" value="{{ old('phone', $admin->admin->phone ?? '') }}" required>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">{{ __('dashboard.profile_image') }}</label>
                        @if($admin->admin->image)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $admin->admin->image) }}" alt="الصورة الحالية" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                                <p>الصورة الحالية</p>
                            </div>
                        @endif
                        <input type="file" id="image" name="image" class="form-input" accept="image/*">
                        <small class="form-help">{{ __('dashboard.profile_image_help') }}</small>
                        @error('image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr class="form-divider">

                    <h3 class="form-section-title">تغيير كلمة المرور (اختياري)</h3>

                    <div class="form-group">
                        <label for="current_password">كلمة المرور الحالية</label>
                        <input type="password" id="current_password" name="current_password" class="form-input @error('current_password') error @enderror">
                        @error('current_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password">كلمة المرور الجديدة</label>
                        <input type="password" id="new_password" name="new_password" class="form-input @error('new_password') error @enderror">
                        @error('new_password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input @error('new_password_confirmation') error @enderror">
                        @error('new_password_confirmation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i>
                            عرض الملف الشخصي
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            العودة للرئيسية
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <style>
        .current-image {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .current-image img {
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .current-image p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        
        .form-help {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
        
        input[type="file"] {
            padding: 8px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
            cursor: pointer;
        }
        
        input[type="file"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        input[type="file"]::-webkit-file-upload-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        input[type="file"]::-webkit-file-upload-button:hover {
            background: #0056b3;
        }
    </style>

    <script src="{{ asset('dashboard/script.js') }}"></script>
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set active navigation item
            const currentPage = 'profile';
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