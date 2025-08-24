@extends('layouts.admin')

@section('title', trans('dashboard.Orders'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ trans('dashboard.Orders') }}</h1>
</div>

<!-- Search Bar -->
<div class="search-section">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="{{ trans('dashboard.Search orders...') }}" class="search-input" id="orderSearch">
    </div>
</div>

<!-- Orders Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ trans('dashboard.Order ID') }}</th>
                <th>{{ trans('dashboard.Customer') }}</th>
                <th>{{ trans('dashboard.Laundry') }}</th>
                <th>{{ trans('dashboard.Service') }}</th>
                <th>{{ trans('dashboard.Price') }}</th>
                <th>{{ trans('dashboard.Status') }}</th>
                <th>{{ trans('dashboard.Created At') }}</th>
                <th>{{ trans('dashboard.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($orders ?? collect()) as $order)
            <tr>
                <td>#{{ $order->id ?? 'N/A' }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td>{{ $order->provider->name ?? 'N/A' }}</td>
                <td>
                    @if($order->target)
                        {{ $order->target->name ?? 'N/A' }}
                    @else
                        {{ trans('dashboard.N/A') }}
                    @endif
                </td>
                <td>
                    @if($order->price > 0)
                        {{ number_format($order->price, 2) }} {{ trans('dashboard.SAR') }}
                    @elseif($order->coins != 0)
                        {{ abs($order->coins) }} {{ trans('dashboard.Coins') }}
                    @else
                        {{ trans('dashboard.Free') }}
                    @endif
                </td>
                <td>
                    <span class="status-badge {{ $order->status ?? 'pending' }}">
                        {{ trans('dashboard.' . ucfirst($order->status ?? 'pending')) }}
                    </span>
                </td>
                <td>{{ $order->created_at ? $order->created_at->format('Y-m-d') : 'N/A' }}</td>
                <td class="actions">
                    <a href="{{ route('admin.orders.view', $order) }}" class="action-btn view" title="{{ trans('dashboard.View') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.orders.edit', $order) }}" class="action-btn edit" title="{{ trans('dashboard.Edit') }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="action-btn delete" onclick="deleteOrder({{ $order->id }})" title="{{ trans('dashboard.Delete') }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">{{ trans('dashboard.No orders found') }}</td>
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
function deleteOrder(orderId) {
    if (confirm('{{ trans("dashboard.Are you sure you want to delete this order?") }}')) {
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
