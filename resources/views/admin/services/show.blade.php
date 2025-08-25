@extends('layouts.admin')

@section('title', 'عرض الخدمة - ' . (($nameData = json_decode($service->getRawOriginal('name'), true)) ? (is_string($nameData[app()->getLocale()] ?? $service->name) ? ($nameData[app()->getLocale()] ?? $service->name) : 'خدمة') : (is_string($service->name) ? $service->name : 'خدمة')))

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>عرض تفاصيل الخدمة</h1>
        <p>
            @php
                $nameData = json_decode($service->getRawOriginal('name'), true);
                $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $service->name) : $service->name;
            @endphp
            {{ is_string($displayName) ? $displayName : 'خدمة' }}
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

    <!-- Service Details -->
    <div class="section-container">
        <!-- Service Profile Header with Image -->
        <div class="service-profile-header">
            <div class="service-avatar">
                @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" alt="صورة الخدمة" class="profile-image" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="default-avatar" style="display: none;">
                        <i class="fas fa-tshirt"></i>
                    </div>
                @else
                    <div class="default-avatar">
                        <i class="fas fa-tshirt"></i>
                    </div>
                @endif
            </div>
            <div class="service-info">
                <h2>
                    @php
                        $nameData = json_decode($service->getRawOriginal('name'), true);
                        $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $service->name) : $service->name;
                    @endphp
                    {{ is_string($displayName) ? $displayName : 'خدمة' }}
                </h2>
                <p class="service-role">
                    <span class="role-badge service">{{ $service->type }}</span>
                </p>
                @if($service->image)
                    <small class="image-info">صورة الخدمة متاحة</small>
                @else
                    <small class="image-info">لا توجد صورة</small>
                @endif
            </div>
        </div>

        <!-- Basic Information -->
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-info-circle"></i> المعلومات الأساسية</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">اسم الخدمة (عربي)</div>
                    <div class="info-value">
                        @php
                            $nameData = json_decode($service->getRawOriginal('name'), true);
                            $displayName = $nameData && is_array($nameData) ? ($nameData['ar'] ?? 'غير محدد') : 'غير محدد';
                        @endphp
                        {{ is_string($displayName) ? $displayName : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">اسم الخدمة (إنجليزي)</div>
                    <div class="info-value">
                        @php
                            $nameData = json_decode($service->getRawOriginal('name'), true);
                            $displayName = $nameData && is_array($nameData) ? ($nameData['en'] ?? 'غير محدد') : 'غير محدد';
                        @endphp
                        {{ is_string($displayName) ? $displayName : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">نوع الخدمة</div>
                    <div class="info-value">{{ $service->type }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الحالة</div>
                    <div class="info-value">
                        @switch($service->status)
                            @case('active')
                                <span class="status-badge active">نشط</span>
                                @break
                            @case('pending')
                                <span class="status-badge pending">في الانتظار</span>
                                @break
                            @case('approved')
                                <span class="status-badge approved">معتمد</span>
                                @break
                            @case('rejected')
                                <span class="status-badge rejected">مرفوض</span>
                                @break
                            @case('inactive')
                                <span class="status-badge inactive">غير نشط</span>
                                @break
                            @default
                                <span class="status-badge">{{ $service->status }}</span>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Information -->
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-building"></i> معلومات المزود</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">نوع المزود</div>
                    <div class="info-value">
                        @if($service->laundry)
                            <span class="badge badge-laundry">مغسلة</span>
                        @elseif($service->agent)
                            <span class="badge badge-agent">وكيل</span>
                        @else
                            <span class="badge badge-unknown">غير محدد</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">اسم المزود</div>
                    <div class="info-value">
                        @if($service->laundry)
                            {{ $service->laundry->user->name ?? 'غير محدد' }}
                        @elseif($service->agent)
                            {{ $service->agent->user->name ?? 'غير محدد' }}
                        @else
                            غير محدد
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">البريد الإلكتروني</div>
                    <div class="info-value">
                        @if($service->provider)
                            {{ $service->provider->email ?? 'غير محدد' }}
                        @else
                            غير محدد
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">رقم الهاتف</div>
                    <div class="info-value">
                        @if($service->provider)
                            {{ $service->provider->phone ?? 'غير محدد' }}
                        @else
                            غير محدد
                        @endif
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
                    <div class="info-label">التكلفة بالنقاط</div>
                    <div class="info-value">
                        @if($service->coin_cost)
                            <span class="coins">{{ $service->coin_cost }} نقطة</span>
                        @else
                            <span class="no-coins">غير محدد</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">السعر بالنقود</div>
                    <div class="info-value">
                        @if($service->price)
                            <span class="price">{{ number_format($service->price, 2) }} ريال</span>
                        @else
                            <span class="no-price">غير محدد</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">الكمية</div>
                    <div class="info-value">{{ $service->quantity ?? 1 }}</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($service->description)
        <div class="info-section">
            <div class="section-header">
                <h3><i class="fas fa-align-left"></i> الوصف</h3>
            </div>
            <div class="description-content">
                @php
                    $descData = json_decode($service->getRawOriginal('description'), true);
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
                    data-entity-id="{{ $service->id }}" 
                    data-entity-type="service"
                    onchange="changeServiceStatus({{ $service->id }}, this.value)">
                <option value="">اختر الحالة</option>
                <option value="pending" {{ $service->status == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="approved" {{ $service->status == 'approved' ? 'selected' : '' }}>معتمد</option>
                <option value="rejected" {{ $service->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
            </select>
        </div>

        <!-- Actions -->
        <div class="action-section">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                تعديل
            </a>
            <a href="{{ route('admin.services') }}" class="btn btn-secondary">
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
        
        @if($service->orders()->count() > 0)
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
                        @foreach($service->orders()->with(['user'])->latest()->get() as $order)
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
                <p>لا توجد طلبات لهذه الخدمة</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
    <style>
        .service-profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .service-avatar {
            position: relative;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #007bff;
        }

        .default-avatar {
            width: 100px;
            height: 100px;
            background: #f8f9fa;
            border: 3px solid #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #007bff;
            font-size: 40px;
        }

        .service-info h2 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 24px;
        }

        .service-role {
            margin: 0 0 10px 0;
        }

        .role-badge.service {
            background: #007bff;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .image-info {
            color: #6c757d;
            font-size: 14px;
        }

        .description-content {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007bff;
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

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-laundry {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-agent {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .badge-unknown {
            background: #f5f5f5;
            color: #616161;
        }

        .price {
            color: #28a745;
            font-weight: 600;
        }

        .coins {
            color: #ffc107;
            font-weight: 600;
        }

        .no-price, .no-coins {
            color: #6c757d;
            font-style: italic;
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
    function changeServiceStatus(serviceId, status) {
        if (!status) return;
        
        fetch(`/admin/services/${serviceId}/status`, {
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
