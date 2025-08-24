@extends('layouts.admin')

@section('title', __('dashboard.agent_management'))

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.agent_management') }}</h1>
    <p>{{ __('dashboard.manage_all_system_agents') }}</p>
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
    
    <a href="{{ route('admin.users.create') }}?role=agent&defaults=1" class="add-btn create-user-btn">
        <i class="fas fa-user-plus"></i>
        {{ __('dashboard.create_agent_user') }}
    </a>
</div>

<!-- Agents Table -->
<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.created_at') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($agents ?? collect()) as $agent)
            <tr>
                <td>{{ $agent->name ?? $agent->user->name ?? __('dashboard.not_specified') }}</td>
                <td>{{ $agent->user->email ?? __('dashboard.not_specified') }}</td>
                <td>{{ $agent->phone ?? $agent->user->phone ?? __('dashboard.not_specified') }}</td>
                <td>
                    @switch($agent->user->status ?? 'pending')
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
                            <span class="status-badge inactive">{{ ucfirst($agent->user->status ?? 'pending') }}</span>
                    @endswitch
                </td>
                <td>{{ $agent->created_at ? $agent->created_at->format('Y-m-d') : __('dashboard.not_specified') }}</td>
                <td class="actions">
                    <a href="{{ route('admin.agents.view', $agent) }}" class="action-btn view" title="{{ __('dashboard.view') }}">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.agents.edit', $agent) }}" class="action-btn edit" title="{{ __('dashboard.edit') }}">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}" style="display: inline;" onsubmit="return confirm('{{ __('dashboard.are_you_sure_delete_agent') }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete" title="{{ __('dashboard.delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">
                    <div class="no-data">
                        <i class="fas fa-users"></i>
                        <p>{{ __('dashboard.no_agents_found') }}</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Simple Pagination -->
@if(isset($agents) && method_exists($agents, 'links'))
    <div class="simple-pagination">
        @if($agents->hasPages())
            <div class="pagination-controls">
                {{-- Previous Page Link --}}
                @if($agents->onFirstPage())
                    <span class="page-link disabled">{{ __('dashboard.previous') }}</span>
                @else
                    <a href="{{ $agents->previousPageUrl() }}" class="page-link">{{ __('dashboard.previous') }}</a>
                @endif

                {{-- Page Numbers --}}
                <div class="page-numbers">
                    @foreach($agents->getUrlRange(1, $agents->lastPage()) as $page => $url)
                        @if($page == $agents->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next Page Link --}}
                @if($agents->hasMorePages())
                    <a href="{{ $agents->nextPageUrl() }}" class="page-link">{{ __('dashboard.next') }}</a>
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

    .action-btn.delete {
        background: #dc3545;
    }

    .action-btn.delete:hover {
        background: #c82333;
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

        .add-btn {
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
