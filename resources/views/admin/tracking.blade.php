@extends('layouts.admin')

@section('title', trans('dashboard.Order Tracking'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ trans('dashboard.Order Tracking') }}</h1>
    <p>{{ trans('dashboard.Real-time order status tracking') }}</p>
</div>

<!-- Summary Cards -->
<div class="summary-cards">
    <div class="card">
        <div class="card-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="card-content">
            <h3>{{ trans('dashboard.Pending') }}</h3>
            <div class="number">{{ $pendingCount ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="card-content">
            <h3>{{ trans('dashboard.In Progress') }}</h3>
            <div class="number">{{ $inProgressCount ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">
            <i class="fas fa-truck"></i>
        </div>
        <div class="card-content">
            <h3>{{ trans('dashboard.On Way') }}</h3>
            <div class="number">{{ $onWayCount ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-content">
            <h3>{{ trans('dashboard.Completed') }}</h3>
            <div class="number">{{ $completedCount ?? 0 }}</div>
        </div>
    </div>
</div>

<!-- Tracking Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ trans('dashboard.Order ID') }}</th>
                <th>{{ trans('dashboard.Customer') }}</th>
                <th>{{ trans('dashboard.Laundry') }}</th>
                <th>{{ trans('dashboard.Status') }}</th>
                <th>{{ trans('dashboard.Last Update') }}</th>
                <th>{{ trans('dashboard.Estimated Delivery') }}</th>
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
                    <span class="status-badge {{ $order->status ?? 'pending' }}">
                        {{ trans('dashboard.' . ucfirst($order->status ?? 'pending')) }}
                    </span>
                </td>
                <td>{{ $order->updated_at ? $order->updated_at->format('Y-m-d H:i') : 'N/A' }}</td>
                <td>{{ $order->meta['estimated_delivery'] ?? trans('dashboard.N/A') }}</td>
                <td class="actions">
                    <a href="{{ route('admin.orders.view', $order) }}" class="action-btn view" title="{{ trans('dashboard.View') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <button class="action-btn update" onclick="updateStatus({{ $order->id }})" title="{{ trans('dashboard.Update Status') }}">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">{{ trans('dashboard.No orders found') }}</td>
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
    const newStatus = prompt('{{ trans("dashboard.Enter new status (pending, in_progress, on_way, completed):") }}');
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
