@extends('layouts.admin')

@section('title', __('dashboard.Edit Order') . ' #' . $order->id)

@section('content')
<!-- Page Title -->
<div class="page-header">
    <h1>{{ __('dashboard.Edit Order') }} #{{ $order->id }}</h1>
</div>

<!-- Alert Messages -->
@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>{{ __('dashboard.Please fix the following errors:') }}</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Edit Order Form -->
<div class="section-container">
    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="edit-form">
        @csrf
        @method('PUT')
        
        <!-- Order Information Section -->
        <div class="form-section">
            <h3>{{ __('dashboard.Order Information') }}</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="status">{{ __('dashboard.Status') }} *</label>
                    <select id="status" name="status" class="form-input @error('status') error @enderror" required>
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>{{ __('dashboard.Pending') }}</option>
                        <option value="in_process" {{ $order->status == 'in_process' ? 'selected' : '' }}>{{ __('dashboard.In Progress') }}</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>{{ __('dashboard.Completed') }}</option>
                        <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>{{ __('dashboard.Canceled') }}</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="target_type">{{ __('dashboard.Target Type') }}</label>
                    <select id="target_type" name="target_type" class="form-input @error('target_type') error @enderror">
                        <option value="">{{ __('dashboard.Select Type') }}</option>
                        <option value="service" {{ $order->target_type == 'service' ? 'selected' : '' }}>{{ __('dashboard.Service') }}</option>
                        <option value="package" {{ $order->target_type == 'package' ? 'selected' : '' }}>{{ __('dashboard.Package') }}</option>
                    </select>
                    @error('target_type')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="target_id">{{ __('dashboard.Target ID') }}</label>
                    <input type="number" id="target_id" name="target_id" class="form-input @error('target_id') error @enderror" value="{{ $order->target_id ?? old('target_id') }}" min="1">
                    @error('target_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="quantity">{{ __('dashboard.Quantity') }}</label>
                    <input type="number" id="quantity" name="quantity" class="form-input @error('quantity') error @enderror" value="{{ $order->quantity ?? old('quantity') }}" min="1">
                    @error('quantity')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">{{ __('dashboard.Price') }} ({{ __('dashboard.SAR') }})</label>
                    <input type="number" id="price" name="price" class="form-input @error('price') error @enderror" value="{{ $order->price ?? old('price') }}" step="0.01" min="0">
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="coins">{{ __('dashboard.Coins') }}</label>
                    <input type="number" id="coins" name="coins" class="form-input @error('coins') error @enderror" value="{{ $order->coins ?? old('coins') }}" min="0">
                    @error('coins')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- User Relationships Section -->
        <div class="form-section">
            <h3>{{ __('dashboard.User Relationships') }}</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="user_id">{{ __('dashboard.Customer') }}</label>
                    <select id="user_id" name="user_id" class="form-input @error('user_id') error @enderror">
                        <option value="">{{ __('dashboard.Select Customer') }}</option>
                        @foreach(\App\Models\User::where('role', 'customer')->get() as $user)
                            <option value="{{ $user->id }}" {{ $order->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="provider_id">{{ __('dashboard.Provider') }}</label>
                    <select id="provider_id" name="provider_id" class="form-input @error('provider_id') error @enderror">
                        <option value="">{{ __('dashboard.Select Provider') }}</option>
                        @foreach(\App\Models\User::whereIn('role', ['admin', 'laundry', 'agent'])->get() as $user)
                            <option value="{{ $user->id }}" {{ $order->provider_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                    @error('provider_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="recipient_id">{{ __('dashboard.Recipient') }}</label>
                    <select id="recipient_id" name="recipient_id" class="form-input @error('recipient_id') error @enderror">
                        <option value="">{{ __('dashboard.Select Recipient') }}</option>
                        @foreach(\App\Models\User::where('role', 'customer')->get() as $user)
                            <option value="{{ $user->id }}" {{ $order->recipient_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('recipient_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="form-section">
            <h3>{{ __('dashboard.Additional Information') }}</h3>
            
            <div class="form-group">
                <label for="notes">{{ __('dashboard.Notes') }}</label>
                <textarea id="notes" name="notes" class="form-input @error('notes') error @enderror" rows="3" placeholder="{{ __('dashboard.Enter any additional notes about this order...') }}">{{ $order->notes ?? old('notes') }}</textarea>
                @error('notes')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                {{ __('dashboard.Save Changes') }}
            </button>
            <a href="{{ route('admin.orders.view', $order) }}" class="btn btn-secondary">
                <i class="fas fa-eye"></i>
                {{ __('dashboard.View Order') }}
            </a>
            <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ __('dashboard.Back to Orders') }}
            </a>
        </div>
    </form>
</div>

<!-- JavaScript for Form Handling -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.edit-form');
    const alerts = document.querySelectorAll('.alert');
    
    // Auto-hide success alerts after 5 seconds
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    });
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __("dashboard.Saving...") }}';
        submitBtn.disabled = true;
        
        // Re-enable button after 10 seconds as fallback
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });
    
    // Add confirmation for form changes
    let formChanged = false;
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });
    
    // Warn user if they try to leave with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '{{ __("dashboard.You have unsaved changes. Are you sure you want to leave?") }}';
        }
    });
    
});










</script>
@endsection

@push('styles')
<style>
    /* Alert Styles */
    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 14px;
        line-height: 1.5;
    }

    .alert i {
        font-size: 18px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert ul {
        margin: 10px 0 0 20px;
        padding: 0;
    }

    .alert li {
        margin-bottom: 5px;
    }

    .alert li:last-child {
        margin-bottom: 0;
    }

    .section-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 30px;
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-section h3 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 600;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #495057;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-input.error {
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .form-help {
        color: #6c757d;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-start;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
    }
</style>
@endpush
