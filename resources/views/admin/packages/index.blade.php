@extends('layouts.admin')

@section('title', 'الباقات')

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>الباقات</h1>
</div>

<!-- Actions -->
<div class="actions-bar">
    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        إضافة باقة جديدة
    </a>
</div>

<!-- Search Bar -->
<div class="search-section">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" placeholder="البحث في الباقات..." class="search-input" id="packageSearch">
    </div>
</div>

<!-- Packages Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>الرقم</th>
                <th>اسم الباقة</th>
                <th>الوصف</th>
                <th>النوع</th>
                <th>السعر</th>
                <th>النقاط</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages as $package)
            <tr>
                <td class="package-id">
                    <span class="id-badge">#{{ $package->id }}</span>
                </td>
                <td>
                    @php
                        $nameData = json_decode($package->getRawOriginal('name'), true);
                        $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $package->name) : $package->name;
                    @endphp
                    {{ is_string($displayName) ? $displayName : 'باقة' }}
                </td>
                <td>
                    @if($package->description)
                        @php
                            $descData = json_decode($package->getRawOriginal('description'), true);
                            $displayDesc = $descData && is_array($descData) ? ($descData[app()->getLocale()] ?? '') : '';
                        @endphp
                        {{ Str::limit($displayDesc, 50) }}
                    @else
                        لا يوجد وصف
                    @endif
                </td>
                <td>{{ ucfirst($package->type ?? 'N/A') }}</td>
                <td>
                    @if($package->price)
                        <span class="price">{{ number_format($package->price, 2) }} ريال</span>
                    @endif
                
                </td>
                <td>
                @if($package->coins_amount)
                        <span class="coins">{{ $package->coins_amount }} نقطة</span>
                    @endif
                </td>
                <td>
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
                </td>
                <td class="actions">
                    <a href="{{ route('admin.packages.show', $package) }}" class="action-btn view" title="عرض">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.packages.edit', $package) }}" class="action-btn edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('admin.packages.customers', $package) }}" class="action-btn customers" title="العملاء المشترين">
                        <i class="fas fa-users"></i>
                    </a>
                    <button class="action-btn delete" onclick="deletePackage({{ $package->id }})" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">لا توجد باقات</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Simple Pagination -->
@if(isset($packages) && method_exists($packages, 'links'))
    <div class="simple-pagination">
        @if($packages->hasPages())
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if($packages->onFirstPage())
                    <span class="page-link disabled">السابق</span>
                @else
                    <a href="{{ $packages->previousPageUrl() }}" class="page-link">السابق</a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach($packages->getUrlRange(1, $packages->lastPage()) as $page => $url)
                        @if($page == $packages->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if($packages->hasMorePages())
                    <a href="{{ $packages->nextPageUrl() }}" class="page-link">التالي</a>
                @else
                    <span class="page-link disabled">التالي</span>
                @endif
            </div>
        @endif
    </div>
@endif
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('packageSearch').addEventListener('input', function() {
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

// Package management functions
function deletePackage(packageId) {
    if (confirm('هل أنت متأكد من حذف هذه الباقة؟')) {
        fetch(`/admin/packages/${packageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء حذف الباقة');
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الباقة');
        });
    }
}
</script>
@endpush

@push('styles')
<style>
    /* Package ID */
    .package-id {
        text-align: center;
        width: 80px;
    }

    .id-badge {
        background: #e9ecef;
        color: #495057;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        font-family: monospace;
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
        background: #138496;
    }

    .action-btn.edit {
        background: #ffc107;
    }

    .action-btn.edit:hover {
        background: #e0a800;
    }

    .action-btn.customers {
        background: #28a745;
    }

    .action-btn.customers:hover {
        background: #1e7e34;
    }

    .action-btn.delete {
        background: #dc3545;
    }

    .action-btn.delete:hover {
        background: #c82333;
    }

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
    }

    .search-input {
        width: 100%;
        padding: 10px 15px 10px 40px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }
</style>
@endpush
