@extends('layouts.admin')

@section('title', __('dashboard.Orders'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.Orders') }}</h1>
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
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="card-content">
            <h3>{{ __('dashboard.Canceled') }}</h3>
            <div class="number">{{ $counts['canceledCount'] ?? 0 }}</div>
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

<!-- Search Bar -->
<div class="search-section">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="{{ __('dashboard.Search orders...') }}" class="search-input" id="orderSearch">
    </div>
</div>

<!-- Orders Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('dashboard.Order ID') }}</th>
                <th>{{ __('dashboard.Customer') }}</th>
                <th>{{ __('dashboard.Laundry') }}</th>
                <th>{{ __('dashboard.Service/Package') }}</th>
                <th>{{ __('dashboard.Price') }}</th>
                <th>{{ __('dashboard.Status') }}</th>
                <th>{{ __('dashboard.Created At') }}</th>
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
                        <div class="target-type">
                            {{ __('dashboard.N/A') }}
                        </div>
                    </div>
                </td>
                <td>
                    @if($order->price > 0)
                        {{ number_format($order->price, 2) }} {{ __('dashboard.SAR') }}
                    @elseif($order->coins != 0)
                        {{ abs($order->coins) }} {{ __('dashboard.Coins') }}
                    @else
                        {{ __('dashboard.Free') }}
                    @endif
                </td>
                <td>
                    <span class="status-badge {{ $order->status ?? 'pending' }}">
                        @if($order->status === 'in_process')
                            @if(app()->getLocale() === 'ar')
                                قيد التنفيذ
                            @else
                                {{ __('dashboard.In Progress') }}
                            @endif
                        @elseif($order->status === 'completed')
                            @if(app()->getLocale() === 'ar')
                                مكتمل
                            @else
                                {{ __('dashboard.Completed') }}
                            @endif
                        @elseif($order->status === 'pending')
                            @if(app()->getLocale() === 'ar')
                                معلق
                            @else
                                {{ __('dashboard.Pending') }}
                            @endif
                        @elseif($order->status === 'canceled')
                            @if(app()->getLocale() === 'ar')
                                ملغي
                            @else
                                {{ __('dashboard.Canceled') }}
                            @endif
                        @else
                            {{ ucfirst($order->status ?? 'pending') }}
                        @endif
                    </span>
                </td>
                <td>{{ $order->created_at ? $order->created_at->format('Y-m-d') : 'N/A' }}</td>
                <td class="actions">
                    <a href="{{ route('admin.orders.view', $order) }}" class="action-btn view" title="{{ __('dashboard.View') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.orders.edit', $order) }}" class="action-btn edit" title="{{ __('dashboard.edit') }}">
                        <i class="fas fa-edit"></i>
                    </a>
                 
                  
                    <button class="action-btn delete" onclick="deleteOrder({{ $order->id }})" title="{{ __('dashboard.Delete') }}">
                        <i class="fas fa-trash"></i>
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

<!-- Simple Pagination -->
@if(isset($orders) && method_exists($orders, 'links'))
    <div class="simple-pagination">
        @if($orders->hasPages())
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if($orders->onFirstPage())
                    <span class="page-link disabled">{{ __('dashboard.previous') }}</span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="page-link">{{ __('dashboard.previous') }}</a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if($page == $orders->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="page-link">{{ __('dashboard.next') }}</a>
                @else
                    <span class="page-link disabled">{{ __('dashboard.next') }}</span>
                @endif
            </div>
        @endif
    </div>
@endif
@endsection

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
    
    .status-badge.completed {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.canceled {
        background: #f8d7da;
        color: #721c24;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border: none;
        border-radius: 50%;
        text-decoration: none;
        color: white;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .action-btn.view {
        background: #17a2b8;
    }

    .action-btn.view:hover {
        background: #158c9e;
        color: white;
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
    
    /* Search Section Enhancement */
    .search-section {
        margin-bottom: 20px;
    }
    
    .search-container {
        position: relative;
        max-width: 400px;
    }
    
    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 1;
    }
    
    .search-input {
        width: 100%;
        padding: 12px 12px 12px 40px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }
    
    /* Pagination Styling */
    .simple-pagination {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .page-link {
        display: inline-block;
        padding: 8px 12px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .page-link:hover:not(.disabled) {
        background: #007bff;
        color: white;
        border-color: #007bff;
        text-decoration: none;
    }

    .page-link.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .page-link.disabled {
        color: #6c757d;
        cursor: not-allowed;
        border-color: #dee2e6;
        background: #f8f9fa;
    }

    .page-numbers {
        display: flex;
        gap: 5px;
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
</style>
@endpush

@push('scripts')
<script>
// Search functionality
document.getElementById('orderSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Order management functions
function updateStatus(orderId) {
    const newStatus = prompt('{{ __("dashboard.Enter new status (pending, in_progress, canceled, completed):") }}');
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

function deleteOrder(orderId) {
    if (confirm('{{ __("dashboard.Are you sure you want to delete this order?") }}')) {
        fetch(`/admin/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        });
    }
}
</script>
@endpush
