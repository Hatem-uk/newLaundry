@extends('layouts.admin')

@section('title', __('dashboard.Service Management'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.Service Management') }}</h1>
</div>

<!-- Actions -->
<div class="actions-bar">
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        إضافة خدمة جديدة
    </a>
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
                <th>{{ __('dashboard.Provider') }}</th>
                <th>{{ __('dashboard.Price') }}</th>
                <th>{{ __('dashboard.Type') }}</th>
                <th>{{ __('dashboard.Status') }}</th>
                <th>{{ __('dashboard.Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($services ?? collect()) as $service)
            <tr>
                <td>
                    @php
                        $nameData = json_decode($service->getRawOriginal('name'), true);
                        $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $service->name) : $service->name;
                    @endphp
                    {{ is_string($displayName) ? $displayName : 'خدمة' }}
                </td>
                <td>
                    @if($service->description)
                        @php
                            $descData = json_decode($service->getRawOriginal('description'), true);
                            $displayDesc = $descData && is_array($descData) ? ($descData[app()->getLocale()] ?? '') : '';
                        @endphp
                        {{ Str::limit($displayDesc, 50) }}
                    @else
                        لا يوجد وصف
                    @endif
                </td>
                <td>
                    @if($service->laundry)
                        <span class="provider-badge laundry">{{ $service->laundry->user->name ?? 'مغسلة' }}</span>
                    @elseif($service->agent)
                        <span class="provider-badge agent">{{ $service->agent->user->name ?? 'وكيل' }}</span>
                    @else
                        <span class="provider-badge unknown">غير محدد</span>
                    @endif
                </td>
                <td>
                    @if($service->price)
                        <span class="price">{{ number_format($service->price, 2) }} ريال</span>
                    @endif
                    @if($service->coin_cost)
                        <span class="coins">{{ $service->coin_cost }} نقطة</span>
                    @endif
                </td>
                <td>{{ ucfirst($service->type ?? 'N/A') }}</td>
                <td>
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
                </td>
                <td class="actions">
                    <a href="{{ route('admin.services.show', $service) }}" class="action-btn view" title="{{ __('dashboard.View') }}">
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
                <td colspan="7" class="text-center">لا توجد خدمات</td>
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
    .provider-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }

    .provider-badge.laundry {
        background: #e3f2fd;
        color: #1976d2;
    }

    .provider-badge.agent {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .provider-badge.unknown {
        background: #f5f5f5;
        color: #616161;
    }

    .price {
        color: #28a745;
        font-weight: 600;
        display: block;
    }

    .coins {
        color: #ffc107;
        font-weight: 600;
        display: block;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.approved {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge.inactive {
        background: #e2e3e5;
        color: #383d41;
    }

    .actions-bar {
        margin-bottom: 20px;
        padding: 15px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
        color: white;
    }

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
    if (confirm('هل أنت متأكد من حذف هذه الخدمة؟')) {
        fetch(`/admin/services/${serviceId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء حذف الخدمة');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الخدمة');
        });
    }
}
</script>
@endpush
