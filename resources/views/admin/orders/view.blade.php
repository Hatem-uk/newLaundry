<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>عرض الطلب - موج</title>
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
                <a href="{{ route('admin.orders') }}" class="nav-item active" data-page="orders">
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
                <h1>عرض الطلب #{{ $order->id }}</h1>
            </div>

            <!-- Order Details -->
            <div class="section-container">
                <div class="order-details">
                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">رقم الطلب</div>
                            <div class="detail-value">#{{ $order->id }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الحالة</div>
                            <div class="detail-value">
                                @if($order->status == 'pending')
                                    <span class="status-badge pending">قيد الانتظار</span>
                                @elseif($order->status == 'in_process')
                                    <span class="status-badge active">قيد المعالجة</span>
                                @elseif($order->status == 'completed')
                                    <span class="status-badge success">مكتمل</span>
                                @elseif($order->status == 'canceled')
                                    <span class="status-badge blocked">ملغي</span>
                                @else
                                    <span class="status-badge">{{ $order->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">العميل</div>
                            <div class="detail-value">{{ $order->user->name ?? 'غير محدد' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">المغسلة</div>
                            <div class="detail-value">
                                @if($order->provider && $order->provider->name)
                                    {{ is_string($order->provider->name) ? $order->provider->name : (json_decode($order->provider->getRawOriginal('name'), true)['ar'] ?? 'غير محدد') }}
                                @else
                                    غير محدد
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">الخدمة</div>
                            <div class="detail-value">
                            @if($order->target && $order->target->name)
                                {{ is_string($order->target->name) ? $order->target->name : (json_decode($order->target->getRawOriginal('name'), true)['ar'] ?? 'غير محدد') }}
                            @else
                                غير محدد
                            @endif
                        </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الكمية</div>
                            <div class="detail-value">{{ $order->quantity ?? 1 }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">السعر</div>
                            <div class="detail-value">{{ number_format($order->price ?? 0, 2) }} ر.س</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">العملات</div>
                            <div class="detail-value">{{ $order->coins ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">تاريخ الطلب</div>
                            <div class="detail-value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">آخر تحديث</div>
                            <div class="detail-value">{{ $order->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="detail-row">
                        <div class="detail-item full-width">
                            <div class="detail-label">ملاحظات</div>
                            <div class="detail-value">{{ $order->notes }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        تعديل الطلب
                    </a>
                    <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        العودة للطلبات
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
            const currentPage = 'orders';
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
