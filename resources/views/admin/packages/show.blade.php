@extends('layouts.admin')

@section('title', 'عرض الباقة - ' . (($nameData = json_decode($package->getRawOriginal('name'), true)) ? (is_string($nameData[app()->getLocale()] ?? $package->name) ? ($nameData[app()->getLocale()] ?? $package->name) : 'باقة') : (is_string($package->name) ? $package->name : 'باقة')))

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>عرض تفاصيل الباقة</h1>
        <p>
            @php
                $nameData = json_decode($package->getRawOriginal('name'), true);
                $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $package->name) : $package->name;
            @endphp
            {{ is_string($displayName) ? $displayName : 'باقة' }}
        </p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Package Details -->
    <div class="section-container">
        <!-- Package Profile Header -->
        <div class="package-profile-header">
            <div class="package-avatar">
                <div class="default-avatar">
                    <i class="fas fa-gift"></i>
                </div>
            </div>
            <div class="package-info">
                <h2>
                    @php
                        $nameData = json_decode($package->getRawOriginal('name'), true);
                        $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $package->name) : $package->name;
                    @endphp
                    {{ is_string($displayName) ? $displayName : 'باقة' }}
                </h2>
                <p class="package-role">
                    <span class="role-badge package">{{ $package->type }}</span>
                </p>
                <small class="package-info-text">باقة نقاط</small>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-info-circle"></i> المعلومات الأساسية</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">اسم الباقة (عربي)</div>
                    <div class="info-value">
                        @php
                            $nameData = json_decode($package->getRawOriginal('name'), true);
                            $displayName = $nameData && is_array($nameData) ? ($nameData['ar'] ?? 'غير محدد') : 'غير محدد';
                        @endphp
                        {{ is_string($displayName) ? $displayName : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">اسم الباقة (إنجليزي)</div>
                    <div class="info-value">
                        @php
                            $nameData = json_decode($package->getRawOriginal('name'), true);
                            $displayName = $nameData && is_array($nameData) ? ($nameData['en'] ?? 'غير محدد') : 'غير محدد';
                        @endphp
                        {{ is_string($displayName) ? $displayName : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">نوع الباقة</div>
                    <div class="info-value">{{ $package->type }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الحالة</div>
                    <div class="info-value">
                        @switch($package->status)
                            @case('active')
                                <span class="status-badge active">نشط</span>
                                @break
                            @case('inactive')
                                <span class="status-badge inactive">غير نشط</span>
                                @break
                            @default
                                <span class="status-badge">{{ $package->status }}</span>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-dollar-sign"></i> معلومات التسعير</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">السعر</div>
                    <div class="info-value">
                        <span class="price">{{ number_format($package->price, 2) }} ريال</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">عدد النقاط</div>
                    <div class="info-value">
                        <span class="coins">{{ $package->coins_amount }} نقطة</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">نسبة النقاط للسعر</div>
                    <div class="info-value">
                        <span class="ratio">{{ number_format($package->coins_amount / $package->price, 2) }} نقطة/ريال</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($package->description)
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-align-left"></i> الوصف</h3>
            </div>
            <div class="description-content">
                @php
                    $descData = json_decode($package->getRawOriginal('description'), true);
                    $displayDesc = $descData && is_array($descData) ? ($descData[app()->getLocale()] ?? '') : '';
                @endphp
                @if($displayDesc)
                    <div class="description-text">{{ $displayDesc }}</div>
                @else
                    <div class="no-description">لا يوجد وصف متاح</div>
                @endif
            </div>
        </div>
        @endif

        <!-- Status Change -->
        <div class="status-dropdown">
            <span class="status-label">تغيير الحالة:</span>
            <select class="status-select" 
                    data-entity-id="{{ $package->id }}" 
                    data-entity-type="package"
                    onchange="changePackageStatus({{ $package->id }}, this.value)">
                <option value="">اختر الحالة</option>
                <option value="active" {{ $package->status == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="inactive" {{ $package->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="action-section">
            <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                تعديل
            </a>
            <a href="{{ route('admin.packages.customers', $package) }}" class="btn btn-success">
                <i class="fas fa-users"></i>
                العملاء المشترين
            </a>
            <a href="{{ route('admin.packages') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Orders Section -->
    <div class="section-container">
        <div class="section-header">
            <h3><i class="fas fa-shopping-cart"></i> الطلبات</h3>
        </div>
        
        @if($package->orders()->count() > 0)
            <div class="orders-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($package->orders()->with(['user'])->latest()->get() as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'غير محدد' }}</td>
                            <td class="order-amount">
                                @if($order->coins > 0)
                                    <span class="coins">{{ $order->coins }} نقطة</span>
                                @endif
                                @if($order->price > 0)
                                    <span class="price">{{ $order->price }} ريال</span>
                                @endif
                            </td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="status-badge order-pending">في الانتظار</span>
                                        @break
                                    @case('in_process')
                                        <span class="status-badge order-in_process">قيد المعالجة</span>
                                        @break
                                    @case('completed')
                                        <span class="status-badge order-completed">مكتمل</span>
                                        @break
                                    @case('canceled')
                                        <span class="status-badge order-canceled">ملغي</span>
                                        @break
                                    @default
                                        <span class="status-badge">{{ $order->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-shopping-cart"></i>
                <p>لا توجد طلبات لهذه الباقة</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
    <style>
        .package-profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .package-avatar {
            position: relative;
        }

        .default-avatar {
            width: 100px;
            height: 100px;
            background: #f8f9fa;
            border: 3px solid #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #28a745;
            font-size: 40px;
        }

        .package-info h2 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 24px;
        }

        .package-role {
            margin: 0 0 10px 0;
        }

        .role-badge.package {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .package-info-text {
            color: #6c757d;
            font-size: 14px;
        }

        .description-content {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }

        .description-text {
            color: #333;
            line-height: 1.6;
        }

        .no-description {
            color: #6c757d;
            font-style: italic;
        }

        .status-dropdown {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .status-label {
            font-weight: 600;
            color: #495057;
        }

        .status-select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background: #fff;
            color: #495057;
        }

        .price {
            color: #28a745;
            font-weight: 600;
        }

        .coins {
            color: #ffc107;
            font-weight: 600;
        }

        .ratio {
            color: #17a2b8;
            font-weight: 600;
        }

        .action-section {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .no-data i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
@endpush

@push('scripts')
<script>
    function changePackageStatus(packageId, status) {
        if (!status) return;
        
        fetch(`/admin/packages/${packageId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء تغيير الحالة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تغيير الحالة');
        });
    }
</script>
@endpush
