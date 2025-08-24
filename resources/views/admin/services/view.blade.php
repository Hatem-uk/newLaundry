<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>عرض الخدمة - موج</title>
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
                <h1>عرض الخدمة</h1>
            </div>

            <!-- Service Details -->
            <div class="section-container">
                <div class="service-details">
                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">اسم الخدمة (عربي)</div>
                            <div class="detail-value">{{ json_decode($service->getRawOriginal('name'), true)['ar'] ?? 'غير محدد' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">اسم الخدمة (إنجليزي)</div>
                            <div class="detail-value">{{ json_decode($service->getRawOriginal('name'), true)['en'] ?? 'غير محدد' }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">السعر</div>
                            <div class="detail-value">{{ number_format($service->price, 2) }} ر.س</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">العملات</div>
                            <div class="detail-value">{{ $service->coins ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">المغسلة</div>
                            <div class="detail-value">
                            @if($service->laundry)
                                {{ is_string($service->laundry->name) ? $service->laundry->name : (json_decode($service->laundry->getRawOriginal('name'), true)['ar'] ?? 'غير محدد') }}
                            @else
                                غير محدد
                            @endif
                        </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الحالة</div>
                            <div class="detail-value">
                                @if($service->is_active)
                                    <span class="status-badge active">نشط</span>
                                @else
                                    <span class="status-badge blocked">غير نشط</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item full-width">
                            <div class="detail-label">الوصف (عربي)</div>
                            <div class="detail-value">{{ json_decode($service->getRawOriginal('description'), true)['ar'] ?? 'غير محدد' }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item full-width">
                            <div class="detail-label">الوصف (إنجليزي)</div>
                            <div class="detail-value">{{ json_decode($service->getRawOriginal('description'), true)['en'] ?? 'غير محدد' }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">تاريخ الإنشاء</div>
                            <div class="detail-value">{{ $service->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">آخر تحديث</div>
                            <div class="detail-value">{{ $service->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        تعديل الخدمة
                    </a>
                    <a href="{{ route('admin.services') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        العودة للخدمات
                    </a>
                </div>
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
