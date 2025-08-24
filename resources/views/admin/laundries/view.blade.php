@extends('layouts.admin')

@section('title', 'عرض المغسلة - ' . (($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name))

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>عرض تفاصيل المغسلة</h1>
        <p>{{ ($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name }}</p>
    </div>

    <!-- Laundry Details -->
    <div class="section-container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Basic Information -->
        <div class="info-section">
            <h3>المعلومات الأساسية</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">اسم المغسلة:</span>
                    <span class="info-value">{{ ($nameData = json_decode($laundry->getRawOriginal('name'), true)) ? ($nameData[app()->getLocale()] ?? $laundry->name) : $laundry->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">البريد الإلكتروني:</span>
                    <span class="info-value">{{ $laundry->user->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">رقم الهاتف:</span>
                    <span class="info-value">{{ $laundry->phone }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">الحالة:</span>
                    <span class="status-badge {{ $laundry->user->status }}">
                        @switch($laundry->user->status)
                            @case('approved')
                                <span class="status-badge active">نشط</span>
                                @break
                            @case('pending')
                                <span class="status-badge pending">في الانتظار</span>
                                @break
                            @case('rejected')
                                <span class="status-badge blocked">مرفوض</span>
                                @break
                            @default
                                <span class="status-badge inactive">غير نشط</span>
                        @endswitch
                    </span>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="info-section">
            <h3>معلومات الموقع</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">المدينة:</span>
                    <span class="info-value">{{ $laundry->city->name ?? 'غير محدد' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">العنوان:</span>
                    <span class="info-value">{{ ($addressData = json_decode($laundry->getRawOriginal('address'), true)) ? ($addressData[app()->getLocale()] ?? $laundry->address) : $laundry->address }}</span>
                </div>
                @if($laundry->latitude && $laundry->longitude)
                <div class="info-item">
                    <span class="info-label">الإحداثيات:</span>
                    <span class="info-value">{{ $laundry->latitude }}, {{ $laundry->longitude }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Business Information -->
        <div class="info-section">
            <h3>معلومات العمل</h3>
            <div class="info-grid">
                @if($laundry->description)
                <div class="info-item full-width">
                    <span class="info-label">الوصف:</span>
                    <span class="info-value">{{ is_string($laundry->description) ? $laundry->description : '' }}</span>
                </div>
                @endif
                @if($laundry->working_hours)
                <div class="info-item">
                    <span class="info-label">ساعات العمل:</span>
                    <span class="info-value">{{ is_string($laundry->working_hours) ? $laundry->working_hours : '' }}</span>
                </div>
                @endif
                @if($laundry->delivery_radius)
                <div class="info-item">
                    <span class="info-label">نطاق التوصيل:</span>
                    <span class="info-value">{{ $laundry->delivery_radius }} كم</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">نشط:</span>
                    <span class="status-badge {{ $laundry->is_active ? 'active' : 'inactive' }}">
                        {{ $laundry->is_active ? 'نعم' : 'لا' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        @if($laundry->website || $laundry->facebook || $laundry->instagram || $laundry->whatsapp)
        <div class="info-section">
            <h3>معلومات التواصل</h3>
            <div class="info-grid">
                @if($laundry->website)
                <div class="info-item">
                    <span class="info-label">الموقع الإلكتروني:</span>
                    <span class="info-value">
                        <a href="{{ $laundry->website }}" target="_blank" class="link">{{ $laundry->website }}</a>
                    </span>
                </div>
                @endif
                @if($laundry->facebook)
                <div class="info-item">
                    <span class="info-label">فيسبوك:</span>
                    <span class="info-value">
                        <a href="{{ $laundry->facebook }}" target="_blank" class="link">{{ $laundry->facebook }}</a>
                    </span>
                </div>
                @endif
                @if($laundry->instagram)
                <div class="info-item">
                    <span class="info-label">انستغرام:</span>
                    <span class="info-value">
                        <a href="{{ $laundry->instagram }}" target="_blank" class="link">{{ $laundry->instagram }}</a>
                    </span>
                </div>
                @endif
                @if($laundry->whatsapp)
                <div class="info-item">
                    <span class="info-label">واتساب:</span>
                    <span class="info-value">
                        <a href="https://wa.me/{{ $laundry->whatsapp }}" target="_blank" class="link">{{ $laundry->whatsapp }}</a>
                    </span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Statistics -->
        <div class="info-section">
            <h3>الإحصائيات</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">{{ $laundry->orders_count ?? 0 }}</div>
                    <div class="stat-label">إجمالي الطلبات</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $laundry->services_count ?? 0 }}</div>
                    <div class="stat-label">الخدمات المتاحة</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $laundry->rating ?? 'غير محدد' }}</div>
                    <div class="stat-label">التقييم</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.laundries.edit', $laundry) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                تعديل المغسلة
            </a>
            
            @if($laundry->user->status === 'pending')
                <form method="POST" action="{{ route('admin.laundries.approve', $laundry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i>
                        الموافقة
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.laundries.reject', $laundry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i>
                        الرفض
                    </button>
                </form>
            @endif

            @if($laundry->user->status === 'approved')
                <form method="POST" action="{{ route('admin.laundries.suspend', $laundry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-pause"></i>
                        تعليق
                    </button>
                </form>
            @endif

            @if($laundry->user->status === 'suspended')
                <form method="POST" action="{{ route('admin.laundries.reactivate', $laundry) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play"></i>
                        إعادة التفعيل
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.laundries') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .info-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .info-section h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 18px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        font-size: 14px;
    }

    .info-value {
        color: #333;
        font-size: 16px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.blocked, .status-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge.inactive {
        background: #e2e3e5;
        color: #383d41;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        margin-top: 15px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: #007bff;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
    }

    .link {
        color: #007bff;
        text-decoration: none;
    }

    .link:hover {
        text-decoration: underline;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #1e7e34;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .btn-outline {
        background: transparent;
        color: #6c757d;
        border: 1px solid #6c757d;
    }

    .btn-outline:hover {
        background: #6c757d;
        color: white;
    }
</style>
@endpush
