@extends('layouts.admin')

@section('title', 'عرض المستخدم - ' . $user->name)

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>عرض تفاصيل المستخدم</h1>
        <p>{{ $user->name }}</p>
    </div>

    <!-- User Details -->
    <div class="section-container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="user-details">
            <div class="detail-row">
                <div class="detail-label">الاسم:</div>
                <div class="detail-value">{{ $user->name }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">البريد الإلكتروني:</div>
                <div class="detail-value">
                    {{ $user->email }}
                    @if($user->email_verified_at)
                        <span class="status-badge verified">
                            <i class="fas fa-check-circle"></i>
                            محقق
                        </span>
                    @else
                        <span class="status-badge unverified">
                            <i class="fas fa-exclamation-circle"></i>
                            غير محقق
                        </span>
                    @endif
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">رقم الهاتف:</div>
                <div class="detail-value">{{ $user->phone ?? 'غير محدد' }}</div>
            </div>
            @if($user->phone && ($user->admin || $user->agent || $user->laundry || $user->customer))
                <div class="detail-row">
                    <div class="detail-label">رقم الهاتف (الملف الشخصي):</div>
                    <div class="detail-value">
                        @if($user->admin)
                            {{ $user->admin->phone ?? 'غير محدد' }}
                        @elseif($user->agent)
                            {{ $user->agent->phone ?? 'غير محدد' }}
                        @elseif($user->laundry)
                            {{ $user->laundry->phone ?? 'غير محدد' }}
                        @elseif($user->customer)
                            {{ $user->customer->phone ?? 'غير محدد' }}
                        @endif
                    </div>
                </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">الدور:</div>
                <div class="detail-value">
                    @switch($user->role)
                        @case('admin')
                            <span class="role-badge admin">مدير</span>
                            @break
                        @case('laundry')
                            <span class="role-badge laundry">مغسلة</span>
                            @break
                        @case('agent')
                            <span class="role-badge agent">وكيل</span>
                            @break
                        @case('worker')
                            <span class="role-badge worker">عامل</span>
                            @break
                        @case('customer')
                            <span class="role-badge customer">عميل</span>
                            @break
                        @default
                            <span class="role-badge">{{ ucfirst($user->role) }}</span>
                    @endswitch
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">الحالة:</div>
                <div class="detail-value">
                    @switch($user->status)
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
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">تاريخ الإنشاء:</div>
                <div class="detail-value">{{ $user->created_at->format('Y-m-d H:i') }}</div>
            </div>
            @if($user->role === 'customer' && $user->customer)
                <div class="detail-row">
                    <div class="detail-label">رصيد النقاط:</div>
                    <div class="detail-value">
                        <span class="coins-badge">
                            <i class="fas fa-coins"></i>
                            {{ number_format($user->customer->coins) }} نقطة
                        </span>
                    </div>
                </div>
            @endif
            @if($user->email_verified_at)
                <div class="detail-row">
                    <div class="detail-label">تاريخ التحقق من البريد:</div>
                    <div class="detail-value">{{ $user->email_verified_at->format('Y-m-d H:i') }}</div>
                </div>
            @endif
            @if($user->fcm_tocken)
                <div class="detail-row">
                    <div class="detail-label">رمز الإشعارات:</div>
                    <div class="detail-value">
                        <code class="fcm-token">{{ Str::limit($user->fcm_tocken, 50) }}</code>
                        @if(strlen($user->fcm_tocken) > 50)
                            <small class="text-muted">({{ strlen($user->fcm_tocken) }} حرف)</small>
                        @endif
                    </div>
                </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">تاريخ الإنشاء:</div>
                <div class="detail-value">{{ $user->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">آخر تحديث:</div>
                <div class="detail-value">{{ $user->updated_at->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                تعديل المستخدم
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .user-details {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #666;
        font-size: 14px;
        min-width: 150px;
    }

    .detail-value {
        color: #333;
        font-size: 16px;
        text-align: left;
    }

    .role-badge, .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .role-badge.admin {
        background: #dc3545;
        color: white;
    }

    .role-badge.agent {
        background: #17a2b8;
        color: white;
    }

    .role-badge.customer {
        background: #28a745;
        color: white;
    }

    .role-badge.laundry {
        background: #6f42c1;
        color: white;
    }

    .role-badge.worker {
        background: #fd7e14;
        color: white;
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

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
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

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .coins-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: #fff3cd;
        color: #856404;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.verified {
        background: #d4edda;
        color: #155724;
        margin-right: 10px;
    }

    .status-badge.unverified {
        background: #fff3cd;
        color: #856404;
        margin-right: 10px;
    }

    .fcm-token {
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        color: #6c757d;
        border: 1px solid #dee2e6;
    }

    .text-muted {
        color: #6c757d !important;
        font-size: 10px;
    }
</style>
@endpush
