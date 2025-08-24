@extends('layouts.admin')

@section('title', __('dashboard.Service Management'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.Service Management') }}</h1>
</div>

<!-- Search Bar -->
<div class="search-section">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="{{ __('dashboard.Search services...') }}" class="search-input" id="serviceSearch">
    </div>
</div>

<!-- Services Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('dashboard.Name') }}</th>
                <th>{{ __('dashboard.Description') }}</th>
                <th>{{ __('dashboard.Price') }}</th>
                <th>{{ __('dashboard.Category') }}</th>
                <th>{{ __('dashboard.Status') }}</th>
                <th>{{ __('dashboard.Created At') }}</th>
                <th>{{ __('dashboard.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($services ?? collect()) as $service)
            <tr>
                <td>{{ $service->name ?? 'N/A' }}</td>
                <td>{{ Str::limit($service->description ?? '', 50) }}</td>
                <td>
                    @if($service->price)
                        {{ number_format($service->price, 2) }} {{ __('dashboard.SAR') }}
                    @elseif($service->coin_cost)
                        {{ $service->coin_cost }} {{ __('dashboard.Coins') }}
                    @else
                        {{ __('dashboard.Free') }}
                    @endif
                </td>
                <td>{{ ucfirst($service->type ?? 'N/A') }}</td>
                <td>
                    <span class="status-badge {{ $service->status ?? 'active' }}">
                        {{ __('dashboard.' . ucfirst($service->status ?? 'active')) }}
                    </span>
                </td>
                <td>{{ $service->created_at ? $service->created_at->format('Y-m-d') : 'N/A' }}</td>
                <td class="actions">
                    <a href="{{ route('admin.services.view', $service) }}" class="action-btn view" title="{{ __('dashboard.View') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.services.edit', $service) }}" class="action-btn edit" title="{{ __('dashboard.Edit') }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="action-btn delete" onclick="deleteService({{ $service->id }})" title="{{ __('dashboard.Delete') }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">{{ __('dashboard.No services found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Simple Pagination -->
@if(isset($services) && method_exists($services, 'links'))
    <div class="simple-pagination">
        @if($services->hasPages())
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if($services->onFirstPage())
                    <span class="page-link disabled">{{ __('dashboard.previous') }}</span>
                @else
                    <a href="{{ $services->previousPageUrl() }}" class="page-link">{{ __('dashboard.previous') }}</a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                        @if($page == $services->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if($services->hasMorePages())
                    <a href="{{ $services->nextPageUrl() }}" class="page-link">{{ __('dashboard.next') }}</a>
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
        box-shadow: 0 2px 44px rgba(0,0,0,0.1);
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
document.getElementById('serviceSearch').addEventListener('input', function() {
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

// Service management functions
function deleteService(serviceId) {
    if (confirm('{{ __("dashboard.Are you sure you want to delete this service?") }}')) {
        fetch(`/admin/services/${serviceId}`, {
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
