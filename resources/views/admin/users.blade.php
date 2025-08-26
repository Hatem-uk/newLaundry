@extends('layouts.admin')

@section('title', __('dashboard.user_management'))

@section('content')
<div class="page-header">
    <h1>{{ __('dashboard.user_management') }}</h1>
    <p>{{ __('dashboard.manage_all_system_users') }}</p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="search-section">
    <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" placeholder="{{ __('dashboard.search_users') }}" id="userSearch">
    </div>
</div>

<div class="action-section">
    <a href="{{ route('admin.users.create') }}" class="add-btn">
        <i class="fas fa-plus"></i>
        {{ __('dashboard.add_new_user') }}
    </a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.role') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.created_at') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? __('dashboard.not_specified') }}</td>
                    <td>
                        @switch($user->role)
                            @case('admin')
                                <span class="role-badge admin">{{ __('dashboard.admin') }}</span>
                                @break
                            @case('laundry')
                                <span class="role-badge laundry">{{ __('dashboard.laundry') }}</span>
                                @break
                            @case('agent')
                                <span class="role-badge agent">{{ __('dashboard.agent') }}</span>
                                @break
                            @case('worker')
                                <span class="role-badge worker">{{ __('dashboard.worker') }}</span>
                                @break
                            @case('customer')
                                <span class="role-badge customer">{{ __('dashboard.customer') }}</span>
                                @break
                            @default
                                <span class="role-badge">{{ ucfirst($user->role) }}</span>
                        @endswitch
                    </td>
                    <td>
                        @switch($user->status)
                            @case('approved')
                                <span class="status-badge active">{{ __('dashboard.active') }}</span>
                                @break
                            @case('pending')
                                <span class="status-badge pending">{{ __('dashboard.pending') }}</span>
                                @break
                            @case('rejected')
                                <span class="status-badge rejected">{{ __('dashboard.rejected') }}</span>
                                @break
                            @default
                                <span class="status-badge inactive">{{ ucfirst($user->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.users.view', $user) }}" class="action-btn view" title="{{ __('dashboard.view') }}">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit" title="{{ __('dashboard.edit') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="action-btn delete" title="{{ __('dashboard.delete') }}" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="no-data">
                            <i class="fas fa-users"></i>
                            <p>{{ __('dashboard.no_users_found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

 

<!-- Alternative Simple Pagination -->
@if(isset($users) && method_exists($users, 'links'))
    <div class="simple-pagination">
        @if($users->hasPages())
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if($users->onFirstPage())
                    <span class="page-link disabled">{{ __('dashboard.previous') }}</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="page-link">{{ __('dashboard.previous') }}</a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if($page == $users->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="page-link">{{ __('dashboard.next') }}</a>
                @else
                    <span class="page-link disabled">{{ __('dashboard.next') }}</span>
                @endif
            </div>
        @endif
    </div>
@endif


@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('userSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Delete confirmation
function confirmDelete(userId, userName) {
    if (confirm(`{{ __("dashboard.are_you_sure_delete_user") }}: ${userName}?`)) {
        // Create a form and submit it for deletion
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add DELETE method
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

@push('styles')
<style>
    .role-badge, .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .role-badge.admin {
        background: #dc3545;
        color: white;
    }

    .role-badge.laundry {
        background: #6f42c1;
        color: white;
    }

    .role-badge.agent {
        background: #17a2b8;
        color: white;
    }

    .role-badge.worker {
        background: #fd7e14;
        color: white;
    }

    .role-badge.customer {
        background: #28a745;
        color: white;
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

    .no-data {
        padding: 40px 20px;
        text-align: center;
        color: #6c757d;
    }

    .no-data i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }

    .no-data p {
        margin: 0;
        font-size: 14px;
    }

    .pagination-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .pagination-info {
        margin: 20px 0 10px 0;
        text-align: center;
        color: #6c757d;
        font-size: 14px;
    }

    .pagination-info p {
        margin: 0;
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

    .add-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .add-btn:hover {
        background: #0056b3;
        color: white;
        text-decoration: none;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 6px;
        border: 1px solid transparent;
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
</style>
@endpush
