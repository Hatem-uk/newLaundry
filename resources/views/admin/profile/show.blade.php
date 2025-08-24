<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>الملف الشخصي - موج</title>
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
                <h1>الملف الشخصي</h1>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Profile Information -->
            <div class="section-container">
                <div class="profile-details">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            @if($admin->admin->image)
                                <img src="{{ asset('storage/' . $admin->admin->image) }}" alt="صورة الملف الشخصي" class="profile-image">
                            @else
                                <div class="default-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                            @endif
                        </div>
                        <div class="profile-info">
                            <h2>{{ $admin->name }}</h2>
                            <p class="role">مدير النظام</p>
                            @if($admin->admin->image)
                                <small class="image-info">صورة الملف الشخصي متاحة</small>
                            @else
                                <small class="image-info">لا توجد صورة للملف الشخصي</small>
                            @endif
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">الاسم الكامل</div>
                            <div class="detail-value">{{ $admin->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">البريد الإلكتروني</div>
                            <div class="detail-value">{{ $admin->email }}</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">رقم الهاتف</div>
                            <div class="detail-value">{{ $admin->admin->phone ?? 'غير محدد' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">الدور</div>
                            <div class="detail-value">
                                <span class="status-badge active">مدير</span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">تاريخ الانضمام</div>
                            <div class="detail-value">{{ $admin->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">آخر تحديث</div>
                            <div class="detail-value">{{ $admin->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        تعديل الملف الشخصي
                    </a>
                    <button class="btn btn-danger" onclick="showDeleteModal()">
                        <i class="fas fa-trash"></i>
                        حذف الحساب
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>حذف الحساب</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p><strong>تحذير:</strong> هذا الإجراء لا يمكن التراجع عنه. سيتم حذف حسابك نهائياً.</p>
                <form method="POST" action="{{ route('admin.profile.destroy') }}" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="form-group">
                        <label for="password">كلمة المرور للتأكيد</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmation">اكتب "delete" للتأكيد</label>
                        <input type="text" id="confirmation" name="confirmation" class="form-input" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">حذف الحساب نهائياً</button>
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Profile Image Styling */
        .profile-avatar {
            position: relative;
            margin-left: 20px;
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .profile-image:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        
        .default-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .default-avatar i {
            font-size: 60px;
            color: white;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .profile-info h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
        }
        
        .profile-info .role {
            margin: 0 0 8px 0;
            color: #7f8c8d;
            font-size: 16px;
            font-weight: 500;
        }
        
        .image-info {
            color: #27ae60;
            font-size: 14px;
            font-weight: 500;
            display: block;
            margin-top: 5px;
        }
        
        .image-info:empty::before {
            content: "لا توجد صورة للملف الشخصي";
            color: #e74c3c;
        }
        
        /* Enhanced Profile Details */
        .profile-details {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 500;
        }
        
        .status-badge.active {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Action Buttons */
        .action-section {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-left: 0;
                margin-bottom: 20px;
            }
            
            .detail-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .action-section {
                flex-direction: column;
                align-items: center;
            }
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

        // Modal functions
        function showDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
