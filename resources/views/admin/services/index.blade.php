@extends('layouts.admin')

@section('title', __('dashboard.services'))

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>{{ __('dashboard.services') }}</h1>
        <p>{{ __('dashboard.manage_services') }}</p>
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

    <!-- Actions -->
    <div class="actions-bar">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            {{ __('dashboard.add_new_service') }}
        </a>
    </div>

    <!-- Services Table -->
    <div class="section-container">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('dashboard.image') }}</th>
                        <th>{{ __('dashboard.name') }}</th>
                        <th>{{ __('dashboard.provider') }}</th>
                        <th>{{ __('dashboard.type') }}</th>
                        <th>{{ __('dashboard.price') }}</th>
                        <th>{{ __('dashboard.coins') }}</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('dashboard.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td>
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" 
                                     alt="{{ $service->name }}" 
                                     class="service-thumbnail"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="default-thumbnail" style="display: none;">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                            @else
                                <div class="default-thumbnail">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="service-name">
                                @php
                                    $nameData = json_decode($service->getRawOriginal('name'), true);
                                    $displayName = $nameData && is_array($nameData) ? ($nameData[app()->getLocale()] ?? $service->name) : $service->name;
                                @endphp
                                {{ is_string($displayName) ? $displayName : 'Service' }}
                            </div>
                            @if($service->description)
                                <small class="service-description">
                                    @php
                                        $descData = json_decode($service->getRawOriginal('description'), true);
                                        $displayDesc = $descData && is_array($descData) ? ($descData[app()->getLocale()] ?? '') : '';
                                    @endphp
                                    {{ Str::limit($displayDesc, 50) }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($service->laundry)
                                <span class="badge badge-laundry">مغسلة</span>
                                <div>{{ $service->laundry->user->name ?? 'غير محدد' }}</div>
                            @elseif($service->agent)
                                <span class="badge badge-agent">وكيل</span>
                                <div>{{ $service->agent->user->name ?? 'غير محدد' }}</div>
                            @else
                                <span class="badge badge-unknown">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            <span class="service-type">{{ $service->type }}</span>
                        </td>
                        <td>
                            @if($service->price)
                                <span class="price">{{ number_format($service->price, 2) }} ريال</span>
                            @else
                                <span class="no-price">-</span>
                            @endif
                        </td>
                        <td>
                            @if($service->coin_cost)
                                <span class="coins">{{ $service->coin_cost }} نقطة</span>
                            @else
                                <span class="no-coins">-</span>
                            @endif
                        </td>
                        <td>
                            @switch($service->status)
                                @case('active')
                                    <span class="status-badge active">{{ __('dashboard.active') }}</span>
                                    @break
                                @case('pending')
                                    <span class="status-badge pending">{{ __('dashboard.pending') }}</span>
                                    @break
                                @case('approved')
                                    <span class="status-badge approved">{{ __('dashboard.approved') }}</span>
                                    @break
                                @case('rejected')
                                    <span class="status-badge rejected">{{ __('dashboard.rejected') }}</span>
                                    @break
                                @case('inactive')
                                    <span class="status-badge inactive">{{ __('dashboard.inactive') }}</span>
                                    @break
                                @default
                                    <span class="status-badge">{{ $service->status }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.services.show', $service) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="{{ __('dashboard.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.services.edit', $service) }}" 
                                   class="btn btn-sm btn-warning" 
                                   title="{{ __('dashboard.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service) }}" 
                                      method="POST" 
                                      class="d-inline delete-form"
                                      onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="{{ __('dashboard.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="no-data">
                                <i class="fas fa-tshirt"></i>
                                <p>{{ __('dashboard.no_services_found') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($services->hasPages())
            <div class="pagination-wrapper">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
    <style>
        .actions-bar {
            margin-bottom: 20px;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .service-thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        .default-thumbnail {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .service-name {
            font-weight: 600;
            color: #333;
        }

        .service-description {
            color: #6c757d;
            font-size: 12px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-laundry {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-agent {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .badge-unknown {
            background: #f5f5f5;
            color: #616161;
        }

        .service-type {
            font-weight: 600;
            color: #495057;
        }

        .price {
            color: #28a745;
            font-weight: 600;
        }

        .coins {
            color: #ffc107;
            font-weight: 600;
        }

        .no-price, .no-coins {
            color: #6c757d;
            font-style: italic;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }

        .delete-form {
            display: inline;
        }

        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
@endpush
