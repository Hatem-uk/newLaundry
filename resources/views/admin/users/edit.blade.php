@extends('layouts.admin')

@section('title', __('dashboard.edit_user') . ' - ' . $user->name)

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>{{ __('dashboard.edit_user') }}</h1>
        <p>{{ $user->name }}</p>
    </div>

    <!-- Edit Form -->
    <div class="section-container">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="edit-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3 class="form-section-title">{{ __('dashboard.basic_information') }}</h3>
                
                <div class="form-group">
                    <label for="name">{{ __('dashboard.name') }} *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{ __('dashboard.email') }} *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @if($user->email_verified_at)
                        <small class="text-success">
                            <i class="fas fa-check-circle"></i>
                            {{ __('dashboard.email_verified_at') }} {{ $user->email_verified_at->format('Y-m-d') }}
                        </small>
                    @else
                        <small class="text-warning">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ __('dashboard.email_not_verified') }}
                        </small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="phone">{{ __('dashboard.phone') }}</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="role">{{ __('dashboard.role') }} *</label>
                    <select id="role" name="role" required class="form-input">
                        <option value="">{{ __('dashboard.select_role') }}</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>{{ __('dashboard.admin') }}</option>
                        <option value="laundry" {{ old('role', $user->role) == 'laundry' ? 'selected' : '' }}>{{ __('dashboard.laundry') }}</option>
                        <option value="agent" {{ old('role', $user->role) == 'agent' ? 'selected' : '' }}>{{ __('dashboard.agent') }}</option>
                        <option value="worker" {{ old('role', $user->role) == 'worker' ? 'selected' : '' }}>{{ __('dashboard.worker') }}</option>
                        <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>{{ __('dashboard.customer') }}</option>
                    </select>
                    @error('role')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">{{ __('dashboard.status') }} *</label>
                    <select id="status" name="status" required class="form-input">
                        <option value="">{{ __('dashboard.select_status') }}</option>
                        <option value="approved" {{ old('status', $user->status) == 'approved' ? 'selected' : '' }}>{{ __('dashboard.active') }}</option>
                        <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>{{ __('dashboard.pending') }}</option>
                        <option value="rejected" {{ old('status', $user->status) == 'rejected' ? 'selected' : '' }}>{{ __('dashboard.rejected') }}</option>
                        <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                @if($user->role === 'customer')
                    <div class="form-group" id="coins-group">
                        <label for="coins">{{ __('dashboard.coin_balance') }}</label>
                        <input type="number" id="coins" name="coins" value="{{ old('coins', $user->customer->coins ?? 0) }}" min="0" class="form-input">
                        <small class="form-help">{{ __('dashboard.current_coin_balance') }}</small>
                        @error('coins')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <div class="form-group" id="coins-group" style="display: none;">
                        <label for="coins">{{ __('dashboard.coin_balance') }}</label>
                        <input type="number" id="coins" name="coins" value="0" min="0" class="form-input">
                        <small class="form-help">{{ __('dashboard.current_coin_balance') }}</small>
                        @error('coins')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Technical Information -->
            <div class="form-section">
                <h3 class="form-section-title">{{ __('dashboard.technical_information') }}</h3>
                
                <div class="form-group">
                    <label for="fcm_tocken">{{ __('dashboard.fcm_token') }}</label>
                    <input type="text" id="fcm_tocken" name="fcm_tocken" value="{{ old('fcm_tocken', $user->fcm_tocken) }}" class="form-input">
                    <small class="form-help">{{ __('dashboard.device_token_notifications') }}</small>
                    @error('fcm_tocken')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>{{ __('dashboard.email_verification_status') }}</label>
                    <div class="verification-status">
                        @if($user->email_verified_at)
                            <span class="status-badge verified">
                                <i class="fas fa-check-circle"></i>
                                {{ __('dashboard.verified_at') }} {{ $user->email_verified_at->format('Y-m-d H:i') }}
                            </span>
                            <button type="button" class="btn btn-sm btn-outline" onclick="removeEmailVerification()">
                                {{ __('dashboard.remove_verification') }}
                            </button>
                        @else
                            <span class="status-badge unverified">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ __('dashboard.not_verified') }}
                            </span>
                            <button type="button" class="btn btn-sm btn-success" onclick="markEmailAsVerified()">
                                {{ __('dashboard.mark_as_verified') }}
                            </button>
                        @endif
                    </div>
                    <input type="hidden" id="email_verified_action" name="email_verified_action" value="">
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3 class="form-section-title">{{ __('dashboard.contact_information') }}</h3>
                
                @if($user->admin)
                    <div class="form-group">
                        <label for="admin_phone">{{ __('dashboard.phone') }} ({{ __('dashboard.admin') }})</label>
                        <input type="tel" id="admin_phone" name="admin_phone" value="{{ old('admin_phone', $user->admin->phone ?? '') }}" class="form-input">
                        @error('admin_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_address">{{ __('dashboard.address') }} ({{ __('dashboard.admin') }})</label>
                        <input type="text" id="admin_address" name="admin_address" value="{{ old('admin_address', $user->admin->address ?? '') }}" class="form-input">
                        @error('admin_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                @if($user->agent)
                    <div class="form-group">
                        <label for="agent_phone">{{ __('dashboard.phone') }} ({{ __('dashboard.agent') }})</label>
                        <input type="tel" id="agent_phone" name="agent_phone" value="{{ old('agent_phone', $user->agent->phone ?? '') }}" class="form-input">
                        @error('agent_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="agent_address">{{ __('dashboard.address') }} ({{ __('dashboard.agent') }})</label>
                        <input type="text" id="agent_address" name="agent_address" value="{{ old('agent_address', $user->agent->address ?? '') }}" class="form-input">
                        @error('agent_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                @if($user->laundry)
                    <div class="form-group">
                        <label for="laundry_phone">{{ __('dashboard.phone') }} ({{ __('dashboard.laundry') }})</label>
                        <input type="tel" id="laundry_phone" name="laundry_phone" value="{{ old('laundry_phone', $user->laundry->phone ?? '') }}" class="form-input">
                        @error('laundry_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="laundry_address">{{ __('dashboard.address') }} ({{ __('dashboard.laundry') }})</label>
                        <input type="text" id="laundry_address" name="laundry_address" value="{{ old('laundry_address', $user->laundry->address ?? '') }}" class="form-input">
                        @error('laundry_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                @if($user->customer)
                    <div class="form-group">
                        <label for="customer_phone">{{ __('dashboard.phone') }} ({{ __('dashboard.customer') }})</label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $user->customer->phone ?? '') }}" class="form-input">
                        @error('customer_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_address">{{ __('dashboard.address') }} ({{ __('dashboard.customer') }})</label>
                        <input type="text" id="customer_address" name="customer_address" value="{{ old('customer_address', $user->customer->address ?? '') }}" class="form-input">
                        @error('customer_address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Image/Logo Upload Section -->
            <div class="form-section">
                <h3 class="form-section-title">{{ __('dashboard.profile_image_logo') }}</h3>
                
                @if($user->admin)
                    <div class="form-group">
                        <label for="admin_image">{{ __('dashboard.profile_image') }} ({{ __('dashboard.admin') }})</label>
                        @if($user->admin->image)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $user->admin->image) }}" alt="الصورة الحالية" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                                <p>الصورة الحالية</p>
                            </div>
                        @endif
                        <input type="file" id="admin_image" name="admin_image" class="form-input" accept="image/*">
                        <small class="form-help">{{ __('dashboard.profile_image_help') }}</small>
                        @error('admin_image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                @if($user->customer)
                    <div class="form-group">
                        <label for="customer_image">{{ __('dashboard.profile_image') }} ({{ __('dashboard.customer') }})</label>
                        @if($user->customer->image)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $user->customer->image) }}" alt="الصورة الحالية" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                                <p>الصورة الحالية</p>
                            </div>
                        @endif
                        <input type="file" id="customer_image" name="customer_image" class="form-input" accept="image/*">
                        <small class="form-help">{{ __('dashboard.profile_image_help') }}</small>
                        @error('customer_image')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                @if($user->laundry)
                    <div class="form-group">
                        <label for="laundry_logo">{{ __('dashboard.logo') }} ({{ __('dashboard.laundry') }})</label>
                        @if($user->laundry->logo)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $user->laundry->logo) }}" alt="الشعار الحالي" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                                <p>الشعار الحالي</p>
                            </div>
                        @endif
                        <input type="file" id="laundry_logo" name="laundry_logo" class="form-input" accept="image/*">
                        <small class="form-help">{{ __('dashboard.logo_help') }}</small>
                        @error('laundry_logo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                @if($user->agent)
                    <div class="form-group">
                        <label for="agent_logo">{{ __('dashboard.logo') }} ({{ __('dashboard.agent') }})</label>
                        @if($user->agent->logo)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $user->agent->logo) }}" alt="الشعار الحالي" style="max-width: 100px; height: auto; margin-bottom: 10px;">
                                <p>الشعار الحالي</p>
                            </div>
                        @endif
                        <input type="file" id="agent_logo" name="agent_logo" class="form-input" accept="image/*">
                        <small class="form-help">{{ __('dashboard.logo_help') }}</small>
                        @error('agent_logo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Password Section -->
            <div class="form-section">
                <h3 class="form-section-title">{{ __('dashboard.password') }}</h3>
                <p class="form-help">{{ __('dashboard.leave_empty_no_change') }}</p>
                
                <div class="form-group">
                    <label for="password">{{ __('dashboard.new_password') }}</label>
                    <input type="password" id="password" name="password" class="form-input">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">{{ __('dashboard.confirm_password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input">
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('dashboard.save_changes') }}
                </button>
                <a href="{{ route('admin.users.view', $user) }}" class="btn btn-secondary">
                    <i class="fas fa-eye"></i>
                    {{ __('dashboard.view_user') }}
                </a>
                <a href="{{ route('admin.users') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-right"></i>
                    {{ __('dashboard.back_to_list') }}
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-edit-forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-edit-forms.js') }}"></script>
    <script>
        // Role change handler
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const contactSection = document.querySelector('.form-section:nth-child(3)');
            const coinsGroup = document.getElementById('coins-group');
            
            if (roleSelect && contactSection && coinsGroup) {
                roleSelect.addEventListener('change', function() {
                    // Show/hide relevant contact fields based on role
                    const role = this.value;
                    const contactFields = contactSection.querySelectorAll('.form-group');
                    
                    contactFields.forEach(field => {
                        field.style.display = 'none';
                    });
                    
                    // Show/hide coins field based on role
                    if (role === 'customer') {
                        coinsGroup.style.display = 'block';
                    } else {
                        coinsGroup.style.display = 'none';
                        document.getElementById('coins').value = 0;
                    }
                    
                    if (role === 'admin') {
                        contactSection.querySelector('[for="admin_phone"]').parentElement.style.display = 'block';
                        contactSection.querySelector('[for="admin_address"]').parentElement.style.display = 'block';
                    } else if (role === 'agent') {
                        contactSection.querySelector('[for="agent_phone"]').parentElement.style.display = 'block';
                        contactSection.querySelector('[for="agent_address"]').parentElement.style.display = 'block';
                    } else if (role === 'laundry') {
                        contactSection.querySelector('[for="laundry_phone"]').parentElement.style.display = 'block';
                        contactSection.querySelector('[for="laundry_address"]').parentElement.style.display = 'block';
                    } else if (role === 'customer') {
                        contactSection.querySelector('[for="customer_phone"]').parentElement.style.display = 'block';
                        contactSection.querySelector('[for="customer_address"]').parentElement.style.display = 'block';
                    }
                });

                // Trigger initial role change
                roleSelect.dispatchEvent(new Event('change'));
            }
        });

        // Email verification functions
        function markEmailAsVerified() {
            document.getElementById('email_verified_action').value = 'verify';
            document.querySelector('.verification-status').innerHTML = 
                '<span class="status-badge verified"><i class="fas fa-check-circle"></i> {{ __("dashboard.will_be_marked_verified") }}</span>';
        }

        function removeEmailVerification() {
            document.getElementById('email_verified_action').value = 'unverify';
            document.querySelector('.verification-status').innerHTML = 
                '<span class="status-badge unverified"><i class="fas fa-exclamation-circle"></i> {{ __("dashboard.verification_will_be_removed") }}</span>';
        }
    </script>
@endpush
