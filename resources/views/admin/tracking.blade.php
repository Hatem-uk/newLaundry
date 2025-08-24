@extends('layouts.admin')

@section('title', __('dashboard.Order Tracking'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.Order Tracking') }}</h1>
    <p>{{ __('dashboard.Real-time order status tracking') }}</p>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="card">
        <div class="card-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="card-content">
            <h3>{{ __('dashboard.Pending') }}</h3>
            <div class="number">{{ $counts['pendingCount'] ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="card-content">
            <h3>{{ __('dashboard.In Progress') }}</h3>
            <div class="number">{{ $counts['inProgressCount'] ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">
            <i class="fas fa-truck"></i>
        </div>
        <div class="card-content">
            <h3>{{ __('dashboard.On Way') }}</h3>
            <div class="number">{{ $counts['onWayCount'] ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-content">
            <h3>{{ __('dashboard.Completed') }}</h3>
            <div class="number">{{ $counts['completedCount'] ?? 0 }}</div>
        </div>
    </div>
</div>

<!-- Tracking Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('dashboard.Order ID') }}</th>
                <th>{{ __('dashboard.Customer') }}</th>
                <th>{{ __('dashboard.Laundry') }}</th>
                <th>{{ __('dashboard.Service/Package') }}</th>
                <th>{{ __('dashboard.Status') }}</th>
                <th>{{ __('dashboard.Last Update') }}</th>
                <th>{{ __('dashboard.Estimated Delivery') }}</th>
                <th>{{ __('dashboard.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($orders ?? collect()) as $order)
            <tr>
                <td>#{{ $order->id ?? 'N/A' }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td>{{ $order->provider->name ?? 'N/A' }}</td>
                <td>
                    <div class="target-info">
                        <div class="target-name">
                            {{ __('dashboard.ID') }}: {{ $order->target_id ?? 'N/A' }}
                        </div>
                        <div class="target-type">
                            @if($order->target_type === 'service')
                                <span class="type-badge service">
                                    @if(app()->getLocale() === 'ar')
                                        خدمة
                                    @else
                                        {{ __('dashboard.Service') }}
                                    @endif
                                </span>
                            @elseif($order->target_type === 'package')
                                <span class="type-badge package">
                                    @if(app()->getLocale() === 'ar')
                                        باقة
                                    @else
                                        {{ __('dashboard.Package') }}
                                    @endif
                                </span>
                            @else
                                @if(app()->getLocale() === 'ar')
                                    {{ ucfirst($order->target_type ?? 'غير متوفر') }}
                                @else
                                    {{ ucfirst($order->target_type ?? 'N/A') }}
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <span class="status-badge {{ $order->status ?? 'pending' }}">
                        {{ __('dashboard.' . ucfirst($order->status ?? 'pending')) }}
                    </span>
                </td>
                <td>{{ $order->updated_at ? $order->updated_at->format('Y-m-d H:i') : 'N/A' }}</td>
                <td>{{ $order->meta['estimated_delivery'] ?? __('dashboard.N/A') }}</td>
                <td class="actions">
                    <a href="{{ route('admin.orders.view', $order) }}" class="action-btn view" title="{{ __('dashboard.View') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="action-btn update" onclick="updateStatus({{ $order->id }})" title="{{ __('dashboard.Update Status') }}">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">{{ __('dashboard.No orders found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if(method_exists(($orders ?? collect()), 'hasPages') && ($orders ?? collect())->hasPages())
<div class="pagination-container">
    {{ ($orders ?? collect())->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
// Update order status function
function updateStatus(orderId) {
    const newStatus = prompt('{{ __("dashboard.Enter new status (pending, in_progress, completed, canceled):") }}');
    if (newStatus) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: newStatus })
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endpush

@push('styles')
<style>
    /* Enhanced Order Details Styles */
    .target-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .target-name {
        font-weight: 600;
        color: #1a237e;
        font-size: 14px;
    }
    
    .target-type {
        font-size: 12px;
        color: #6c757d;
    }
    
    .type-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .type-badge.service {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .type-badge.package {
        background: #f3e5f5;
        color: #7b1fa2;
    }
    
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        text-align: center;
        min-width: 80px;
    }
    
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.in_process {
        background: #cce5ff;
        color: #004085;
    }
    
    .status-badge.on_way {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-badge.completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.canceled {
        background: #f8d7da;
        color: #721c24;
    }
    
    .actions {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
    
    .action-btn {
        padding: 6px 8px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .action-btn.view {
        background: #17a2b8;
        color: white;
    }
    
    .action-btn.update {
        background: #ffc107;
        color: #212529;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Summary Cards Enhancement */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .summary-cards .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .summary-cards .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: white;
        font-size: 20px;
    }
    
    .card-content h3 {
        margin: 0 0 10px 0;
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }
    
    .card-content .number {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    /* Table Enhancements */
    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background: #f8f9fa;
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }
    
    .data-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    /* Pagination Styling */
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .pagination-container .pagination {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
@endpush
