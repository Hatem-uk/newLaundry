<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تعديل الخدمة - موج</title>
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
                <a href="{{ route('admin.services') }}" class="nav-item active" data-page="services">
                    <i class="fas fa-file-alt"></i>
                    <span>ادارة الخدمات</span>
                </a>
                <a href="{{ route('admin.orders') }}" class="nav-item" data-page="orders">
                    <i class="fas fa-list"></i>
                    <span>الطلبات</span>
                </a>
                <a href="{{ route('admin.tracking') }}" class="nav-item" data-page="tracking">
                    <i class="fas fa-truck"></i>
                    <span>متابعة حالات الطلبات</span>
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
                <h1>تعديل الخدمة</h1>
            </div>

            <!-- Edit Service Form -->
            <div class="section-container">
                <form method="POST" action="{{ route('admin.services.update', $service) }}" class="edit-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="name">اسم الخدمة (عربي)</label>
                        <input type="text" id="name" name="name" class="form-input @error('name') error @enderror" value="{{ json_decode($service->getRawOriginal('name'), true)['ar'] ?? old('name') }}" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name_en">اسم الخدمة (إنجليزي)</label>
                        <input type="text" id="name_en" name="name_en" class="form-input @error('name_en') error @enderror" value="{{ json_decode($service->getRawOriginal('name'), true)['en'] ?? old('name_en') }}" required>
                        @error('name_en')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

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

                    <div class="form-group">
                        <label for="price">السعر</label>
                        <input type="number" id="price" name="price" class="form-input @error('price') error @enderror" value="{{ $service->price ?? old('price') }}" step="0.01" min="0" required>
                        @error('price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="coins">العملات</label>
                        <input type="number" id="coins" name="coins" class="form-input @error('coins') error @enderror" value="{{ $service->coins ?? old('coins') }}" min="0">
                        @error('coins')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_active">الحالة</label>
                        <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                            <option value="1" {{ ($service->is_active == 1) ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ ($service->is_active == 0) ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('is_active')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            حفظ التغييرات
                        </button>
                        <a href="{{ route('admin.services.view', $service) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i>
                            عرض الخدمة
                        </a>
                        <a href="{{ route('admin.services') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            العودة للخدمات
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
            const currentPage = 'services';
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
