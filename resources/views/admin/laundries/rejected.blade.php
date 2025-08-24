@extends('layouts.admin')

@section('title', __('dashboard.rejected_laundries'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>{{ __('dashboard.rejected_laundries') }}</h1>
            <p>{{ __('dashboard.manage_rejected_laundry_requests') }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.laundries') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                {{ __('dashboard.back_to_laundries') }}
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="fas fa-check-circle"></i>
            <strong>{{ __('dashboard.success') }}!</strong>
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="closeAlert('success-alert')">×</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" id="error-alert">
            <i class="fas fa-exclamation-circle"></i>
            <strong>{{ __('dashboard.error') }}!</strong>
            {{ session('error') }}
            <button type="button" class="alert-close" onclick="closeAlert('error-alert')">×</button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" id="validation-alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>{{ __('dashboard.validation_errors') }}:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="alert-close" onclick="closeAlert('validation-alert')">×</button>
        </div>
    @endif

    <!-- Rejected Laundries List -->
    <div class="section-container">
        @if($laundries->count() > 0)
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.name') }}</th>
                            <th>{{ __('dashboard.owner') }}</th>
                            <th>{{ __('dashboard.city') }}</th>
                            <th>{{ __('dashboard.phone') }}</th>
                            <th>{{ __('dashboard.rejected_at') }}</th>
                            <th>{{ __('dashboard.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laundries as $laundry)
                            <tr>
                                <td>
                                    <div class="laundry-info">
                                        <div class="laundry-name">
                                            {{ $laundry->name }}
                                        </div>
                                        @if($laundry->description)
                                            <div class="laundry-description">
                                                {{ Str::limit($laundry->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-name">{{ $laundry->user->name }}</div>
                                        <div class="user-email">{{ $laundry->user->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($laundry->city)
                                        {{ $laundry->city->name }}
                                    @else
                                        <span class="text-muted">{{ __('dashboard.not_specified') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($laundry->phone)
                                        {{ $laundry->phone }}
                                    @else
                                        <span class="text-muted">{{ __('dashboard.not_specified') }}</span>
                                    @endif
                                </td>
                                <td>{{ $laundry->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.laundries.view', $laundry) }}" class="action-btn view" title="{{ __('dashboard.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.laundries.approve', $laundry) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="action-btn approve" title="{{ __('dashboard.reconsider') }}" onclick="return confirm('{{ __('dashboard.are_you_sure_approve_laundry') }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($laundries->hasPages())
                    <div class="simple-pagination">
                        @if($laundries->onFirstPage())
                            <span class="page-link disabled">{{ __('dashboard.previous') }}</span>
                        @else
                            <a href="{{ $laundries->previousPageUrl() }}" class="page-link">{{ __('dashboard.previous') }}</a>
                        @endif

                        <span class="page-info">
                            {{ __('dashboard.page') }} {{ $laundries->currentPage() }} {{ __('dashboard.of') }} {{ $laundries->lastPage() }}
                        </span>

                        @if($laundries->hasMorePages())
                            <a href="{{ $laundries->nextPageUrl() }}" class="page-link">{{ __('dashboard.next') }}</a>
                        @else
                            <span class="page-link disabled">{{ __('dashboard.next') }}</span>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="no-data">
                <i class="fas fa-ban"></i>
                <h3>{{ __('dashboard.no_rejected_laundries') }}</h3>
                <p>{{ __('dashboard.all_laundry_requests_approved') }}</p>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="navigation-buttons">
            <a href="{{ route('admin.laundries.pending') }}" class="nav-btn pending-btn">
                <i class="fas fa-clock"></i>
                {{ __('dashboard.pending_laundries') }}
            </a>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-content h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
    }

    .header-content p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .back-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        animation: slideIn 0.3s ease;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert i {
        font-size: 18px;
        flex-shrink: 0;
    }

    .alert strong {
        font-weight: 600;
    }

    .alert-close {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .alert-close:hover {
        opacity: 1;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 18px 20px;
        text-align: right;
        border-bottom: 1px solid #e9ecef;
    }

    .data-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table tr:hover {
        background: #f8f9fa;
        transition: background 0.3s ease;
    }

    .laundry-info {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .laundry-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }

    .laundry-description {
        font-size: 13px;
        color: #6c757d;
        font-style: italic;
    }

    .user-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .user-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .user-email {
        font-size: 13px;
        color: #6c757d;
    }

    .text-muted {
        color: #6c757d !important;
        font-style: italic;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 14px;
    }

    .action-btn.view {
        background: #17a2b8;
    }

    .action-btn.view:hover {
        background: #138496;
        transform: scale(1.1);
    }

    .action-btn.approve {
        background: #28a745;
    }

    .action-btn.approve:hover {
        background: #218838;
        transform: scale(1.1);
    }

    .no-data {
        text-align: center;
        padding: 80px 20px;
        color: #6c757d;
    }

    .no-data i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .no-data h3 {
        margin: 0 0 15px 0;
        color: #495057;
        font-size: 24px;
    }

    .no-data p {
        margin: 0;
        color: #6c757d;
        font-size: 16px;
    }

    .navigation-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .nav-btn {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-btn.pending-btn {
        background: #ffc107;
        color: #212529;
        border: 1px solid #ffc107;
    }

    .nav-btn.pending-btn:hover {
        background: #e0a800;
        border-color: #e0a800;
        transform: translateY(-2px);
        color: #212529;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    .simple-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        padding: 25px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .page-link {
        padding: 10px 16px;
        border: 1px solid #dee2e6;
        color: #007bff;
        text-decoration: none;
        border-radius: 6px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .page-link:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
        text-decoration: none;
        transform: translateY(-2px);
    }

    .page-link.disabled {
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .page-link.disabled:hover {
        background: transparent;
        color: #6c757d;
        transform: none;
    }

    .page-info {
        color: #6c757d;
        font-weight: 500;
        padding: 0 15px;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .header-content h1 {
            font-size: 2rem;
        }

        .data-table th,
        .data-table td {
            padding: 12px 15px;
            font-size: 14px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .simple-pagination {
            flex-direction: column;
            gap: 10px;
            padding: 20px;
        }

        .navigation-buttons {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        });
    }, 5000);

    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        }
    }
</script>
@endpush
