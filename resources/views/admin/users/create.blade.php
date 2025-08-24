@extends('layouts.admin')

@section('title', __('dashboard.add_new_user'))

@section('content')
    <!-- Page Title -->
    <div class="page-header">
        <h1>{{ __('dashboard.add_new_user') }}</h1>
        <p>{{ __('dashboard.create_new_user_account') }}</p>
    </div>

    <!-- Create Form -->
    <div class="section-container">
        @if(session('success'))
            <div class="alert alert-success" id="success-alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="alert-close" onclick="closeAlert('success-alert')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" id="error-alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('error') }}
                <button type="button" class="alert-close" onclick="closeAlert('error-alert')">
                    <i class="fas fa-times"></i>
                </button>
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
                <button type="button" class="alert-close" onclick="closeAlert('validation-alert')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="create-form" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="form-section">
                <h3>{{ __('dashboard.basic_information') }}</h3>
                
                <div class="form-group">
                    <label for="name">{{ __('dashboard.name') }} *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="email">{{ __('dashboard.email') }} *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="phone">{{ __('dashboard.phone') }}</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control">
                </div>

                <div class="form-group">
                    <label for="role">{{ __('dashboard.role') }} *</label>
                    <select id="role" name="role" required class="form-control">
                        <option value="">{{ __('dashboard.select_role') }}</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('dashboard.admin') }}</option>
                        <option value="laundry" {{ old('role') == 'laundry' ? 'selected' : '' }}>{{ __('dashboard.laundry') }}</option>
                        <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>{{ __('dashboard.agent') }}</option>
                        <option value="worker" {{ old('role') == 'worker' ? 'selected' : '' }}>{{ __('dashboard.worker') }}</option>
                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>{{ __('dashboard.customer') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">{{ __('dashboard.status') }} *</label>
                    <select id="status" name="status" required class="form-control">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('dashboard.pending') }}</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>{{ __('dashboard.active') }}</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>{{ __('dashboard.rejected') }}</option>
                    </select>
                </div>

                <div class="form-group" id="coins-group" style="display: none;">
                    <label for="coins">{{ __('dashboard.initial_coin_balance') }}</label>
                    <input type="number" id="coins" name="coins" value="{{ old('coins', 100) }}" min="0" class="form-control">
                    <small class="form-help">{{ __('dashboard.initial_coins_for_customer') }}</small>
                </div>
            </div>

            <!-- Role-Specific Information -->
            <div class="form-section" id="role-specific-info" style="display: none;">
                <h3>{{ __('dashboard.role_specific_information') }}</h3>
                
                <!-- Laundry Fields -->
                <div id="laundry-fields" style="display: none;">
                    <div class="form-group">
                        <label for="laundry_name">{{ __('dashboard.laundry_name') }}</label>
                        <input type="text" id="laundry_name" name="laundry_name" value="{{ old('laundry_name') }}" class="form-control">
                        <small class="form-help">{{ __('dashboard.laundry_name_help') }}</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="laundry_logo">{{ __('dashboard.logo') }}</label>
                        <input type="file" id="laundry_logo" name="laundry_logo" class="form-control" accept="image/*">
                        <small class="form-help">{{ __('dashboard.logo_help') }}</small>
                    </div>
                </div>

                <!-- Worker Fields -->
                <div id="worker-fields" style="display: none;">
                    <div class="form-group">
                        <label for="laundry_id">{{ __('dashboard.laundry') }} *</label>
                        <select id="laundry_id" name="laundry_id" class="form-control">
                            <option value="">{{ __('dashboard.select_laundry') }}</option>
                            @foreach(\App\Models\Laundry::where('is_active', true)->get() as $laundry)
                                <option value="{{ $laundry->id }}" {{ old('laundry_id') == $laundry->id ? 'selected' : '' }}>
                                    {{ $laundry->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-help">{{ __('dashboard.worker_laundry_help') }}</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="position">{{ __('dashboard.position') }}</label>
                        <input type="text" id="position" name="position" value="{{ old('position', 'Worker') }}" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="salary">{{ __('dashboard.salary') }}</label>
                        <input type="number" id="salary" name="salary" value="{{ old('salary', 0) }}" min="0" step="0.01" class="form-control">
                    </div>
                </div>

                <!-- Admin Image Field -->
                <div id="admin-fields" style="display: none;">
                    <div class="form-group">
                        <label for="admin_image">{{ __('dashboard.profile_image') }}</label>
                        <input type="file" id="admin_image" name="admin_image" class="form-control" accept="image/*">
                        <small class="form-help">{{ __('dashboard.profile_image_help') }}</small>
                    </div>
                </div>

                <!-- Customer Image Field -->
                <div id="customer-fields" style="display: none;">
                    <div class="form-group">
                        <label for="customer_image">{{ __('dashboard.profile_image') }}</label>
                        <input type="file" id="customer_image" name="customer_image" class="form-control" accept="image/*">
                        <small class="form-help">{{ __('dashboard.profile_image_help') }}</small>
                    </div>
                </div>

                <!-- Agent Logo Field -->
                <div id="agent-fields" style="display: none;">
                    <div class="form-group">
                        <label for="agent_logo">{{ __('dashboard.logo') }}</label>
                        <input type="file" id="agent_logo" name="agent_logo" class="form-control" accept="image/*">
                        <small class="form-help">{{ __('dashboard.logo_help') }}</small>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="form-section" id="address-section" style="display: none;">
                <h3>{{ __('dashboard.address_information') }}</h3>
                
                <div class="form-group">
                    <label for="city_id">{{ __('dashboard.city') }}</label>
                    <select id="city_id" name="city_id" class="form-control">
                        <option value="">{{ __('dashboard.select_city') }}</option>
                        @php
                            $cities = \App\Models\City::all()->sortBy('name');
                        @endphp
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help">{{ __('dashboard.city_help') }} ({{ $cities->count() }} {{ __('dashboard.cities_available') }})</small>
                </div>
                
                <div class="form-group">
                    <label for="address">{{ __('dashboard.address') }}</label>
                    <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Password Section -->
            <div class="form-section">
                <h3>{{ __('dashboard.password') }}</h3>
                
                <div class="form-group">
                    <label for="password">{{ __('dashboard.password') }} *</label>
                    <input type="password" id="password" name="password" required class="form-control" minlength="6">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">{{ __('dashboard.confirm_password') }} *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control" minlength="6">
                </div>
            </div>

            <!-- Technical Settings -->
            <div class="form-section">
                <h3>{{ __('dashboard.technical_settings') }}</h3>
                
                <div class="form-group">
                    <label for="fcm_tocken">{{ __('dashboard.fcm_token') }}</label>
                    <input type="text" id="fcm_tocken" name="fcm_tocken" value="{{ old('fcm_tocken') }}" class="form-control">
                    <small class="form-help">{{ __('dashboard.fcm_token_help') }}</small>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        {{ __('dashboard.mark_email_verified_immediately') }}
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    {{ __('dashboard.create_user') }}
                </button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i>
                    {{ __('dashboard.back_to_list') }}
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .form-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-section h3 {
        margin: 0 0 20px 0;
        color: #333;
        font-size: 18px;
        border-bottom: 2px solid #007bff;
        padding-bottom: 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #495057;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-help {
        color: #6c757d;
        font-size: 14px;
        margin-top: 5px;
        font-style: italic;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: normal;
    }

    .checkbox-label input[type="checkbox"] {
        width: auto;
        margin: 0;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
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

    .alert ul {
        margin: 0;
        padding-right: 20px;
    }

    .alert li {
        margin-bottom: 5px;
    }

    .alert i {
        margin-right: 8px;
        font-size: 16px;
    }

    .alert strong {
        font-weight: 600;
    }

    .alert {
        position: relative;
    }

    .alert-close {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        color: inherit;
        font-size: 18px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .alert-close:hover {
        opacity: 1;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    /* File input styling */
    input[type="file"].form-control {
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        background-color: #fff;
        cursor: pointer;
    }

    input[type="file"].form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    input[type="file"].form-control::-webkit-file-upload-button {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    input[type="file"].form-control::-webkit-file-upload-button:hover {
        background: #0056b3;
    }
</style>
@endpush

@push('scripts')
<script>
    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.create-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        const roleSelect = document.getElementById('role');
        const coinsGroup = document.getElementById('coins-group');
        const roleSpecificInfo = document.getElementById('role-specific-info');
        const addressSection = document.getElementById('address-section');
        const laundryFields = document.getElementById('laundry-fields');
        const workerFields = document.getElementById('worker-fields');
        const adminFields = document.getElementById('admin-fields');
        const customerFields = document.getElementById('customer-fields');
        const agentFields = document.getElementById('agent-fields');

        // Password confirmation validation
        function validatePassword() {
            if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('{{ __("dashboard.passwords_not_match") }}');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }

        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);

        // Role change handler
        function handleRoleChange() {
            const role = roleSelect.value;
            
            // Hide all role-specific sections first
            roleSpecificInfo.style.display = 'none';
            addressSection.style.display = 'none';
            laundryFields.style.display = 'none';
            workerFields.style.display = 'none';
            adminFields.style.display = 'none';
            customerFields.style.display = 'none';
            agentFields.style.display = 'none';
            
            // Show/hide coins field based on role
            if (role === 'customer') {
                coinsGroup.style.display = 'block';
            } else {
                coinsGroup.style.display = 'none';
                document.getElementById('coins').value = 100;
            }
            
            // Show role-specific information
            if (role) {
                roleSpecificInfo.style.display = 'block';
                addressSection.style.display = 'block';
                
                // Show role-specific fields
                if (role === 'laundry') {
                    laundryFields.style.display = 'block';
                } else if (role === 'worker') {
                    workerFields.style.display = 'block';
                    // Make laundry_id required for workers
                    document.getElementById('laundry_id').required = true;
                } else if (role === 'admin') {
                    adminFields.style.display = 'block';
                } else if (role === 'customer') {
                    customerFields.style.display = 'block';
                } else if (role === 'agent') {
                    agentFields.style.display = 'block';
                }
                
                // For admin, agent, customer - make laundry_id not required
                if (role !== 'worker') {
                    document.getElementById('laundry_id').required = false;
                }
            }
        }

        roleSelect.addEventListener('change', handleRoleChange);

        // Form submission validation
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
            }
        });

        // Handle default values from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const defaultRole = urlParams.get('role');
        const hasDefaults = urlParams.get('defaults') === '1';
        
        if (defaultRole && hasDefaults) {
            roleSelect.value = defaultRole;
            handleRoleChange();
            
            // Set default values based on role
            if (defaultRole === 'laundry') {
                // Set default laundry name
                const laundryNameInput = document.getElementById('laundry_name');
                if (laundryNameInput) {
                    laundryNameInput.value = '{{ __("dashboard.default_laundry_name") }}';
                }
                
                // Set default status to approved for laundry
                document.getElementById('status').value = 'approved';
            } else if (defaultRole === 'agent') {
                // Set default status to approved for agent
                document.getElementById('status').value = 'approved';
            }
        }
        
        // Trigger initial role change on page load
        if (roleSelect.value) {
            handleRoleChange();
        }

        // Enhance city selection with search functionality
        const citySelect = document.getElementById('city_id');
        if (citySelect && citySelect.options.length > 10) {
            // Add search functionality for cities when there are many options
            const citySearchInput = document.createElement('input');
            citySearchInput.type = 'text';
            citySearchInput.placeholder = '{{ __("dashboard.search_cities") }}...';
            citySearchInput.className = 'form-control city-search';
            citySearchInput.style.marginBottom = '10px';
            
            citySelect.parentNode.insertBefore(citySearchInput, citySelect);
            
            citySearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                Array.from(citySelect.options).forEach(option => {
                    if (option.value === '') return; // Keep the placeholder option
                    const cityName = option.text.toLowerCase();
                    option.style.display = cityName.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    });

    // Function to close alerts manually
    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }
    }
</script>
@endpush
