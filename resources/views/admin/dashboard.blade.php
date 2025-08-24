@extends('layouts.admin')

@section('title', __('dashboard.Dashboard'))

@section('content')
<div class="dashboard-content">
    <div class="welcome-section">
        <h1>{{ __('dashboard.Welcome to Dashboard') }}</h1>
        <p>{{ __('dashboard.Overview of General Performance') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Today Orders') }}</h3>
                <div class="number">{{ number_format($stats['today_orders']) }}</div>
                <div class="revenue-periods">
                    <div class="period-item">
                        @if($stats['orders_growth'] >= 0)
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+{{ $stats['orders_growth'] }}% from yesterday</span>
                        @else
                            <i class="fas fa-arrow-down text-danger"></i>
                            <span class="text-danger">{{ $stats['orders_growth'] }}% from yesterday</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-coins"></i>
                <span class="riyal-icon">﷼</span>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Total Revenue') }}</h3>
                <div class="number">
                    <span class="riyal-symbol">﷼</span>
                    {{ number_format($stats['total_revenue'], 2) }}
                </div>
                <div class="revenue-periods">
                    <div class="period-item">
                        @if($stats['revenue_growth'] >= 0)
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+{{ $stats['revenue_growth'] }}% from last month</span>
                        @else
                            <i class="fas fa-arrow-down text-danger"></i>
                            <span class="text-danger">{{ $stats['revenue_growth'] }}% from last month</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Total Users') }}</h3>
                <div class="number">{{ number_format($stats['total_users']) }}</div>
                <div class="revenue-periods">
                    <div class="period-item">
                        @if($stats['users_growth'] >= 0)
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+{{ $stats['users_growth'] }}% this week</span>
                        @else
                            <i class="fas fa-arrow-down text-danger"></i>
                            <span class="text-danger">{{ $stats['users_growth'] }}% this week</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="card-content">
                <h3>{{ __('dashboard.Pending Orders') }}</h3>
                <div class="number">{{ number_format($stats['pending_orders']) }}</div>
                <div class="revenue-periods">
                    <div class="period-item">
                        @if($stats['pending_change'] <= 0)
                            <i class="fas fa-arrow-down text-success"></i>
                            <span class="text-success">{{ $stats['pending_change'] }} from yesterday</span>
                        @else
                            <i class="fas fa-arrow-up text-warning"></i>
                            <span class="text-warning">+{{ $stats['pending_change'] }} from yesterday</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="bottom-section">
        <div class="stats-card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i>
                <h3>{{ __('dashboard.Revenue Statistics') }}</h3>
            </div>
            <p class="card-subtitle">{{ __('dashboard.Monthly revenue overview') }}</p>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">{{ __('dashboard.This Month') }}</span>
                    <span class="stat-value">
                        <span class="riyal-symbol">﷼</span>
                        {{ number_format($stats['this_month_revenue'], 2) }}
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">{{ __('dashboard.Last Month') }}</span>
                    <span class="stat-value">
                        <span class="riyal-symbol">﷼</span>
                        {{ number_format($stats['last_month_revenue'], 2) }}
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">{{ __('dashboard.Growth') }}</span>
                    <span class="stat-value {{ $stats['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $stats['revenue_growth'] >= 0 ? '+' : '' }}{{ $stats['revenue_growth'] }}%
                    </span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">إجمالي الطلبات</span>
                    <span class="stat-value">{{ number_format($stats['total_orders']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">الطلبات المكتملة</span>
                    <span class="stat-value">{{ number_format($stats['completed_orders']) }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">قيد التنفيذ</span>
                    <span class="stat-value">{{ number_format($stats['in_process_orders']) }}</span>
                </div>
            </div>
        </div>

        <div class="orders-card">
            <div class="card-header">
                <i class="fas fa-list-alt"></i>
                <h3>{{ __('dashboard.Recent Orders') }}</h3>
            </div>
            <p class="card-subtitle">{{ __('dashboard.Latest order updates') }}</p>
            <div class="orders-list">
                @forelse($stats['recent_orders'] as $order)
                    <div class="order-item">
                        <div class="order-info">
                            <div class="order-price">
                                <span class="riyal-symbol">﷼</span>
                                {{ number_format($order['price'], 2) }}
                            </div>
                            <div class="laundry-name">{{ $order['target_name'] }}</div>
                            <div class="order-details">
                                طلب #{{ $order['id'] }} • 
                                {{ $order['target_type'] === 'service' ? 'خدمة' : 'باقة' }} • 
                                {{ $order['customer_name'] }}
                            </div>
                            <div class="order-time">{{ $order['created_at']->diffForHumans() }}</div>
                        </div>
                        <span class="status {{ $order['status'] }}">
                            @switch($order['status'])
                                @case('completed')
                                    {{ __('dashboard.Completed') }}
                                    @break
                                @case('in_process')
                                    {{ __('dashboard.In Progress') }}
                                    @break
                                @case('pending')
                                    {{ __('dashboard.Pending') }}
                                    @break
                                @case('canceled')
                                    ملغي
                                    @break
                                @default
                                    {{ $order['status'] }}
                            @endswitch
                        </span>
                    </div>
                @empty
                    <div class="no-orders">
                        <i class="fas fa-inbox"></i>
                        <p>لا توجد طلبات حديثة</p>
                    </div>
                @endforelse
            </div>
            
            @if(count($stats['recent_orders']) > 0)
                <div class="card-footer">
                    <a href="{{ route('admin.orders') }}" class="view-all-btn">
                        <i class="fas fa-eye"></i>
                        عرض جميع الطلبات
                    </a>
                </div>
            @endif
        </div>
    </div>


</div>
@endsection

@push('styles')
<style>
    .text-success {
        color: #28a745 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-warning {
        color: #ffc107 !important;
    }
    
    .order-time {
        font-size: 12px;
        color: #6c757d;
        margin-top: 4px;
    }
    
    .no-orders {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    
    .no-orders i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }
    
    .no-orders p {
        margin: 0;
        font-size: 14px;
    }
    
    .card-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        background: #f8f9fa;
        text-align: center;
    }
    
    .view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: color 0.3s ease;
    }
    
    .view-all-btn:hover {
        color: #0056b3;
        text-decoration: none;
    }
    
    .status.completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status.in_process, .status.in-process {
        background: #fff3cd;
        color: #856404;
    }
    
    .status.pending {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status.canceled {
        background: #e2e3e5;
        color: #383d41;
    }
    
    .summary-cards .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .summary-cards .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .stats-list {
        display: grid;
        gap: 15px;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .stat-label {
        font-weight: 500;
        color: #495057;
    }
    
    .stat-value {
        font-weight: 600;
        color: #2c3e50;
    }
    
    /* Riyal Icon Styles */
    .riyal-symbol, .riyal-icon {
        color: #2c3e50;
        font-weight: bold;
        margin-left: 5px;
        font-size: 0.9em;
    }
    
    .card-icon .riyal-icon {
        position: absolute;
        top: 50%;
        right: 50%;
        transform: translate(50%, -50%);
        font-size: 24px;
        color: rgba(255, 255, 255, 0.8);
        z-index: 2;
    }
    
    .card-icon {
        position: relative;
    }
    
    .card-icon .fas {
        z-index: 1;
    }
    

</style>
@endpush
