@extends('layouts.admin')

@section('title', __('dashboard.View Order'))

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-header">
        <h1>{{ __('dashboard.View Order') }} #{{ $order->id }}</h1>
        <p>{{ __('dashboard.Order Information') }}</p>
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

    <!-- Order Details -->
    <div class="section-container">
        <div class="order-details">
            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Order ID') }}</div>
                    <div class="detail-value">#{{ $order->id }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Status') }}</div>
                    <div class="detail-value">
                        @if($order->status == 'pending')
                            <span class="status-badge pending">{{ __('dashboard.pending') }}</span>
                        @elseif($order->status == 'in_process')
                            <span class="status-badge active">{{ __('dashboard.In Progress') }}</span>
                        @elseif($order->status == 'completed')
                            <span class="status-badge success">{{ __('dashboard.Completed') }}</span>
                        @elseif($order->status == 'canceled')
                            <span class="status-badge blocked">{{ __('dashboard.Canceled') }}</span>
                        @else
                            <span class="status-badge">{{ $order->status }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Customer') }}</div>
                    <div class="detail-value">{{ $order->user->name ?? __('dashboard.not_specified') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Laundry') }}</div>
                    <div class="detail-value">
                        @if($order->provider && $order->provider->name)
                            {{ is_string($order->provider->name) ? $order->provider->name : (json_decode($order->provider->getRawOriginal('name'), true)['ar'] ?? __('dashboard.not_specified')) }}
                        @else
                            {{ __('dashboard.not_specified') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Service/Package') }}</div>
                    <div class="detail-value">
                        <div class="target-info">
                            <div class="target-name">
                                @if($order->target_type === 'service')
                                    <span class="type-badge service">
                                        @if(app()->getLocale() === 'ar')
                                            خدمة
                                        @else
                                            {{ __('dashboard.Service') }}
                                        @endif
                                    </span>
                                @elseif($order->target_type === 'package')
                                    <span class="type-badge package">
                                        @if(app()->getLocale() === 'ar')
                                            باقة
                                        @else
                                            {{ __('dashboard.Package') }}
                                        @endif
                                    </span>
                                @else
                                    @if(app()->getLocale() === 'ar')
                                        {{ ucfirst($order->target_type ?? 'غير متوفر') }}
                                    @else
                                        {{ ucfirst($order->target_type ?? 'N/A') }}
                                    @endif
                                @endif
                            </div>
                            <div class="target-type">
                                {{ $order->getTargetName() }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Quantity') }}</div>
                    <div class="detail-value">{{ $order->quantity ?? 1 }}</div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Price') }}</div>
                    <div class="detail-value price">{{ number_format($order->price ?? 0, 2) }} {{ __('dashboard.SAR') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Coins') }}</div>
                    <div class="detail-value coins">{{ $order->coins ?? 0 }}</div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Date Created') }}</div>
                    <div class="detail-value">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('dashboard.Date Updated') }}</div>
                    <div class="detail-value">{{ $order->updated_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            @if($order->notes)
            <div class="detail-row">
                <div class="detail-item full-width">
                    <div class="detail-label">{{ __('dashboard.Notes') }}</div>
                    <div class="detail-value">{{ $order->notes }}</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="actions-bar">
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                {{ __('dashboard.Edit Order') }}
            </a>
            <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ __('dashboard.Back to Orders') }}
            </a>
                        <form action="{{ route('admin.orders.destroy', $order) }}"
                  method="POST"
                  class="d-inline delete-form"
                  onsubmit="return confirm('{{ __('dashboard.Are you sure you want to delete this order?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-danger"
                        title="{{ __('dashboard.Delete') }}">
                    <i class="fas fa-trash"></i>
                    {{ __('dashboard.Delete') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
    <style>
        .order-details {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .detail-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-item {
            flex: 1;
            min-width: 200px;
        }

        .detail-item.full-width {
            flex: 100%;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .detail-value {
            color: #333;
            font-size: 16px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.active {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-badge.success {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.blocked {
            background: #f8d7da;
            color: #721c24;
        }

        .price {
            color: #28a745;
            font-weight: 600;
        }

        .coins {
            color: #ffc107;
            font-weight: 600;
        }

        .actions-bar {
            margin-top: 20px;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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
            font-size: 14px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            color: white;
        }

        .delete-form {
            display: inline;
        }

        /* Target Info Styling */
        .target-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .target-name {
            font-weight: 600;
            color: #1a237e;
            font-size: 14px;
        }
        
        .target-type {
            font-size: 12px;
            color: #6c757d;
        }
        
        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .type-badge.service {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .type-badge.package {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
                gap: 15px;
            }

            .detail-item {
                min-width: auto;
            }

            .actions-bar {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
@endpush
