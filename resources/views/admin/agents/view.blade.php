<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>عرض الوكيل - {{ $agent->user->name }} - موج</title>
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
                <h1>عرض تفاصيل الوكيل</h1>
                <p>{{ $agent->user->name }}</p>
            </div>

            <!-- Agent Details -->
            <div class="section-container">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="agent-details">
                    <div class="detail-row">
                        <div class="detail-label">الاسم:</div>
                        <div class="detail-value">{{ $agent->user->name }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">البريد الإلكتروني:</div>
                        <div class="detail-value">{{ $agent->user->email }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">رقم الهاتف:</div>
                        <div class="detail-value">{{ $agent->phone ?? 'غير محدد' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">رقم الترخيص:</div>
                        <div class="detail-value">{{ $agent->license_number }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">المدينة:</div>
                        <div class="detail-value">{{ $agent->city->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">العنوان:</div>
                        <div class="detail-value">{{ $agent->address ?? 'غير محدد' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">الحالة:</div>
                        <div class="detail-value">
                            @switch($agent->user->status)
                                @case('pending')
                                    <span class="status-badge pending">في الانتظار</span>
                                    @break
                                @case('approved')
                                    <span class="status-badge active">نشط</span>
                                    @break
                                @case('rejected')
                                    <span class="status-badge blocked">مرفوض</span>
                                    @break
                                @default
                                    <span class="status-badge inactive">غير نشط</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">حالة الوكيل:</div>
                        <div class="detail-value">
                            @if($agent->is_active)
                                <span class="status-badge active">نشط</span>
                            @else
                                <span class="status-badge inactive">غير نشط</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">تاريخ الإنشاء:</div>
                        <div class="detail-value">{{ $agent->user->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">آخر تحديث:</div>
                        <div class="detail-value">{{ $agent->user->updated_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('admin.agents.edit', $agent) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        تعديل الوكيل
                    </a>
                    <a href="{{ route('admin.agents') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
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
