@extends('layouts.admin')

@section('title', __('dashboard.laundry_management'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.laundry_management') }}</h1>
    <p>{{ __('dashboard.manage_all_system_laundries') }}</p>
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

<!-- Action Buttons -->
<div class="action-section">
  
    <a href="{{ route('admin.laundries.create') }}?role=laundry&defaults=1" class="add-btn create-user-btn">
        <i class="fas fa-user-plus"></i>
        {{ __('dashboard.create_laundry_user') }}
    </a>
    <a href="" class="status-btn pending-btn">
        <i class="fas fa-clock"></i>
        {{ __('dashboard.pending') }} ({{ $pendingCount ?? 0 }})
    </a>
    <a href="" class="status-btn rejected-btn">
        <i class="fas fa-times"></i>
        {{ __('dashboard.rejected') }} ({{ $rejectedCount ?? 0 }})
    </a>

</div>

<!-- Search Section -->
<div class="search-section">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" placeholder="{{ __('dashboard.search_laundries') }}" id="laundrySearch">
    </div>
</div>

<!-- Laundries Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.owner') }}</th>
                <th>{{ __('dashboard.city') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.rating') }}</th>
                <th>{{ __('dashboard.created_at') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($laundries ?? collect()) as $laundry)
            <tr>
                <td>{{ $laundry->name ?? __('dashboard.not_specified') }}</td>
                <td>{{ $laundry->user->name ?? __('dashboard.not_specified') }}</td>
                <td>{{ $laundry->city ? $laundry->city->name : __('dashboard.not_specified') }}</td>
                <td>
                    @switch($laundry->user->status ?? 'pending')
                        @case('pending')
                            <span class="status-badge pending">{{ __('dashboard.pending') }}</span>
                            @break
                        @case('approved')
                            <span class="status-badge active">{{ __('dashboard.approved') }}</span>
                            @break
                        @case('rejected')
                            <span class="status-badge rejected">{{ __('dashboard.rejected') }}</span>
                            @break

                        @default
                            <span class="status-badge inactive">{{ __('dashboard.inactive') }}</span>
                    @endswitch
                </td>
                <td>
                    <div class="rating">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= ($laundry->average_rating ?? 0) ? 'filled' : '' }}"></i>
                        @endfor
                        <span class="rating-text">{{ number_format($laundry->average_rating ?? 0, 1) }}</span>
                    </div>
                </td>
                <td>{{ $laundry->created_at ? $laundry->created_at->format('Y-m-d') : __('dashboard.not_specified') }}</td>
                <td class="actions">
                    <a href="{{ route('admin.laundries.view', $laundry) }}" class="action-btn view" title="{{ __('dashboard.view') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    
                    <a href="{{ route('admin.laundries.edit', $laundry) }}" class="action-btn edit" title="{{ __('dashboard.edit') }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if(($laundry->user->status ?? 'pending') === 'pending')
                        <button class="action-btn approve" onclick="approveLaundry({{ $laundry->id }})" title="{{ __('dashboard.approve') }}">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="action-btn reject" onclick="rejectLaundry({{ $laundry->id }})" title="{{ __('dashboard.reject') }}">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                    @if(($laundry->user->status ?? 'pending') === 'approved')
                        <button class="action-btn block" onclick="blockLaundry({{ $laundry->id }})" title="{{ __('dashboard.block') }}">
                            <i class="fas fa-ban"></i>
                        </button>
                    @endif

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">
                    <div class="no-data">
                        <i class="fas fa-tshirt"></i>
                        <p>{{ __('dashboard.no_laundries_found') }}</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Simple Pagination -->
@if(isset($laundries) && method_exists($laundries, 'links'))
    <div class="simple-pagination">
        @if($laundries->hasPages())
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if($laundries->onFirstPage())
                    <span class="page-link disabled">{{ __('dashboard.previous') }}</span>
                @else
                    <a href="{{ $laundries->previousPageUrl() }}" class="page-link">{{ __('dashboard.previous') }}</a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach($laundries->getUrlRange(1, $laundries->lastPage()) as $page => $url)
                        @if($page == $laundries->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if($laundries->hasMorePages())
                    <a href="{{ $laundries->nextPageUrl() }}" class="page-link">{{ __('dashboard.next') }}</a>
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
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        text-align: center;
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
        font-weight: 700;
    }

    .page-header p {
        margin: 0;
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    .alert i {
        font-size: 18px;
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
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 2;
    }

    .search-input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: white;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .action-section {
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .add-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .add-btn:hover {
        background: #0056b3;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .create-user-btn {
        background: #28a745;
    }

    .create-user-btn:hover {
        background: #1e7e34;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .status-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        border-radius: 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .pending-btn {
        background: #ffc107;
        color: #212529;
    }

    .pending-btn:hover {
        background: #e0a800;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    .rejected-btn {
        background: #dc3545;
    }

    .rejected-btn:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }



    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .data-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .data-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table tbody tr {
        border-bottom: 1px solid #f1f3f4;
        transition: background-color 0.3s ease;
    }

    .data-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .data-table td {
        padding: 15px;
        vertical-align: middle;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.rejected {
        background: #f8d7da;
        color: #721c24;
    }



    .status-badge.inactive {
        background: #e2e3e5;
        color: #383d41;
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .rating i {
        color: #ffc107;
        font-size: 14px;
    }

    .rating i.filled {
        color: #ffc107;
    }

    .rating-text {
        margin-left: 8px;
        font-weight: 600;
        color: #495057;
    }

    .actions {
        display: flex;
        gap: 8px;
        justify-content: center;
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
        transform: scale(1.1);
    }

    .action-btn.edit {
        background: #ffc107;
    }

    .action-btn.edit:hover {
        background: #e0a800;
        transform: scale(1.1);
    }

    .action-btn.approve {
        background: #28a745;
    }

    .action-btn.approve:hover {
        background: #1e7e34;
        transform: scale(1.1);
    }

    .action-btn.reject {
        background: #dc3545;
    }

    .action-btn.reject:hover {
        background: #c82333;
        transform: scale(1.1);
    }

    .action-btn.block {
        background: #6c757d;
    }

    .action-btn.block:hover {
        background: #5a6268;
        transform: scale(1.1);
    }



    .no-data {
        padding: 40px 20px;
        text-align: center;
        color: #6c757d;
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 15px;
        display: block;
        color: #dee2e6;
    }

    .no-data p {
        margin: 0;
        font-size: 16px;
        font-weight: 500;
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header {
            padding: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .action-section {
            flex-direction: column;
        }

        .add-btn, .status-btn {
            width: 100%;
            justify-content: center;
        }

        .data-table {
            font-size: 14px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 8px;
        }

        .actions {
            flex-direction: column;
            gap: 5px;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Search functionality
document.getElementById('laundrySearch').addEventListener('input', function() {
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

// Laundry management functions
function approveLaundry(laundryId) {
    if (confirm('{{ __("dashboard.are_you_sure_approve_laundry") }}')) {
        document.getElementById('approve-form-' + laundryId).submit();
    }
}

function rejectLaundry(laundryId) {
    if (confirm('{{ __("dashboard.are_you_sure_reject_laundry") }}')) {
        document.getElementById('reject-form-' + laundryId).submit();
    }
}

function blockLaundry(laundryId) {
    if (confirm('{{ __("dashboard.are_you_sure_block_laundry") }}')) {
        document.getElementById('block-form-' + laundryId).submit();
    }
}


</script>

<!-- Hidden Forms for Laundries -->
@foreach($laundries as $laundry)
                                    <form id="approve-form-{{ $laundry->id }}" method="POST" action="{{ route('admin.laundries.approve', $laundry) }}" style="display: none;">
                    @csrf
                </form>
                <form id="reject-form-{{ $laundry->id }}" method="POST" action="{{ route('admin.laundries.reject', $laundry) }}" style="display: none;">
                    @csrf
                </form>
                <form id="block-form-{{ $laundry->id }}" method="POST" action="{{ route('admin.laundries.block', $laundry) }}" style="display: none;">
                    @csrf
                </form>

@endforeach
@endpush
