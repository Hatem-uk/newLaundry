@extends('layouts.admin')

@section('title', 'عملاء الباقة - ' . (($nameData = json_decode($package->getRawOriginal('name'), true)) ? (is_string($nameData[app()->getLocale()] ?? $package->name) ? ($nameData[app()->getLocale()] ?? $package->name) : 'باقة') : (is_string($package->name) ? $package->name : 'باقة')))

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>عملاء الباقة</h1>
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

    <!-- Package Info -->
    <div class="section-container">
        <div class="package-info">
            <div class="package-header">
                <h3>معلومات الباقة</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">اسم الباقة</div>
                    <div class="info-value">
                        @php
                            $nameData = json_decode($package->getRawOriginal('name'), true);
                            $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $package->name) : $package->name;
                        @endphp
                        {{ is_string($displayName) ? $displayName : 'غير محدد' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">النوع</div>
                    <div class="info-value">{{ $package->type }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">السعر</div>
                    <div class="info-value">{{ number_format($package->price, 2) }} ريال</div>
                </div>
                <div class="info-item">
                    <div class="info-label">النقاط</div>
                    <div class="info-value">{{ $package->coins_amount }} نقطة</div>
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
                <div class="info-item">
                    <div class="info-label">عدد المشترين</div>
                    <div class="info-value">{{ count($customers) }} عميل</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers List -->
    <div class="section-container">
        <div class="section-header">
            <h3><i class="fas fa-users"></i> قائمة العملاء المشترين</h3>
        </div>

        @if(count($customers) > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>اسم العميل</th>
                            <th>البريد الإلكتروني</th>
                            <th>رقم الهاتف</th>
                            <th>تاريخ الشراء</th>
                            <th>المبلغ المدفوع</th>
                            <th>نوع الدفع</th>
                            <th>حالة الطلب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>#{{ $customer['order_id'] }}</td>
                            <td>
                                <div class="customer-name">
                                    {{ $customer['customer_name'] }}
                                </div>
                            </td>
                            <td>{{ $customer['customer_email'] }}</td>
                            <td>{{ $customer['customer_phone'] ?? 'غير محدد' }}</td>
                            <td>{{ $customer['purchase_date']->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($customer['payment_type'] === 'نقدي')
                                    <span class="price">{{ number_format($customer['amount_paid'], 2) }} ريال</span>
                                @else
                                    <span class="coins">{{ $customer['amount_paid'] }} نقطة</span>
                                @endif
                            </td>
                            <td>
                                <span class="payment-type {{ $customer['payment_type'] === 'نقدي' ? 'cash' : 'coins' }}">
                                    {{ $customer['payment_type'] }}
                                </span>
                            </td>
                            <td>
                                @switch($customer['order_status'])
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
                                        <span class="status-badge">{{ $customer['order_status'] }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.users.view', $customer['customer_id']) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="عرض العميل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.view', $customer['order_id']) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="عرض الطلب">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-users"></i>
                <p>لا يوجد عملاء اشتروا هذه الباقة بعد</p>
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="action-section">
        <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            العودة للباقة
        </a>
        <a href="{{ route('admin.packages') }}" class="btn btn-primary">
            <i class="fas fa-list"></i>
            قائمة الباقات
        </a>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
    <style>
        .package-info {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .package-header h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 18px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
        }

        .customer-name {
            font-weight: 600;
            color: #333;
        }

        .payment-type {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .payment-type.cash {
            background: #d4edda;
            color: #155724;
        }

        .payment-type.coins {
            background: #fff3cd;
            color: #856404;
        }

        .price {
            color: #28a745;
            font-weight: 600;
        }

        .coins {
            color: #ffc107;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
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
