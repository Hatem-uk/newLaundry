<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Registration - Laundry Service</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,rgb(103, 206, 250) 0%,rgb(102, 126, 234) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: 20px;
        }

        .header {
            background: linear-gradient(135deg,rgb(103, 206, 250) 0%,rgb(102, 126, 234) 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            opacity: 0.9;
            font-size: 16px;
        }

        .pending-notice {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 20px;
            text-align: center;
            border: 1px solid #ffeaa7;
        }

        .pending-notice i {
            margin-right: 8px;
        }

        .form-container {
            padding: 20px 30px 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group label.required::after {
            content: " *";
            color: #e74c3c;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .form-group input.error,
        .form-group select.error {
            border-color: #e74c3c;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg,rgb(103, 206, 250) 0%,rgb(102, 126, 234) 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
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

        .file-input-wrapper {
            position: relative;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-wrapper .file-input-display {
            border: 2px dashed #e1e5e9;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: #666;
            transition: all 0.3s ease;
        }

        .file-input-wrapper:hover .file-input-display {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }

        .file-input-wrapper input[type="file"]:focus + .file-input-display {
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
        }

        .row {
            display: flex;
            gap: 15px;
        }

        .col {
            flex: 1;
        }

        @media (max-width: 600px) {
            .row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-tie"></i> Agent Registration</h1>
            <p>Join our laundry service network</p>
        </div>

        <div class="pending-notice">
            <i class="fas fa-info-circle"></i>
            <strong>Important:</strong> Your account will be pending approval. You'll be notified once approved by an administrator.
        </div>

        <div class="form-container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="list-style: none; margin: 0; padding: 0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('agent.register') }}" enctype="multipart/form-data" id="agentRegisterForm">
                @csrf
                
                <div class="form-group">
                    <label for="name" class="required">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    <div class="error-message" id="name-error"></div>
                </div>

                <div class="form-group">
                    <label for="email" class="required">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    <div class="error-message" id="email-error"></div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="phone" class="required">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                            <div class="error-message" id="phone-error"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="license_number" class="required">License Number</label>
                            <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                            <div class="error-message" id="license_number-error"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="city_id" class="required">City</label>
                    <select id="city_id" name="city_id" required>
                        <option value="">Select a city</option>
                        <option value="1" {{ old('city_id') == '1' ? 'selected' : '' }}>Riyadh</option>
                        <option value="2" {{ old('city_id') == '2' ? 'selected' : '' }}>Jeddah</option>
                        <option value="3" {{ old('city_id') == '3' ? 'selected' : '' }}>Dammam</option>
                        <option value="4" {{ old('city_id') == '4' ? 'selected' : '' }}>Mecca</option>
                        <option value="5" {{ old('city_id') == '5' ? 'selected' : '' }}>Medina</option>
                    </select>
                    <div class="error-message" id="city_id-error"></div>
                </div>

                <div class="form-group">
                    <label for="address" class="required">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" required>
                    <div class="error-message" id="address-error"></div>
                </div>

                <div class="form-group">
                    <label for="password" class="required">Password</label>
                    <input type="password" id="password" name="password" required>
                    <div class="error-message" id="password-error"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="required">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                    <div class="error-message" id="password_confirmation-error"></div>
                </div>

                <div class="form-group">
                    <label for="image">Profile Logo</label>
                    <div class="file-input-wrapper">
                        <input type="file" id="image" name="image" accept="image/*">
                        <div class="file-input-display">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 24px; margin-bottom: 10px;"></i>
                            <div>Click to upload logo</div>
                            <div style="font-size: 12px; margin-top: 5px;">JPEG, PNG, JPG, GIF up to 2MB</div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-user-plus"></i> Register as Agent
                </button>

                <div class="login-link">
                    Already have an account? <a href="{{ route('agent.login') }}">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('agentRegisterForm');
            const submitBtn = document.getElementById('submitBtn');

            // File input preview
            const fileInput = document.getElementById('image');
            const fileDisplay = document.querySelector('.file-input-display');

            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    fileDisplay.innerHTML = `
                        <i class="fas fa-check-circle" style="color: #28a745; font-size: 24px; margin-bottom: 10px;"></i>
                        <div>${file.name}</div>
                        <div style="font-size: 12px; margin-top: 5px;">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                    `;
                }
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const errors = {};

                // Clear previous errors
                clearErrors();

                // Validate name
                const name = document.getElementById('name').value.trim();
                if (name.length < 2) {
                    errors.name = 'Name must be at least 2 characters long';
                    isValid = false;
                }

                // Validate email
                const email = document.getElementById('email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    errors.email = 'Please enter a valid email address';
                    isValid = false;
                }

                // Validate phone
                const phone = document.getElementById('phone').value.trim();
                if (phone.length < 8) {
                    errors.phone = 'Phone number must be at least 8 digits';
                    isValid = false;
                }

                // Validate license number
                const licenseNumber = document.getElementById('license_number').value.trim();
                if (licenseNumber.length < 3) {
                    errors.license_number = 'License number must be at least 3 characters';
                    isValid = false;
                }

                // Validate city
                const cityId = document.getElementById('city_id').value;
                if (!cityId) {
                    errors.city_id = 'Please select a city';
                    isValid = false;
                }

                // Validate address
                const address = document.getElementById('address').value.trim();
                if (address.length < 5) {
                    errors.address = 'Address must be at least 5 characters long';
                    isValid = false;
                }

                // Validate password
                const password = document.getElementById('password').value;
                if (password.length < 6) {
                    errors.password = 'Password must be at least 6 characters long';
                    isValid = false;
                }

                // Validate password confirmation
                const passwordConfirmation = document.getElementById('password_confirmation').value;
                if (password !== passwordConfirmation) {
                    errors.password_confirmation = 'Passwords do not match';
                    isValid = false;
                }

                // Display errors if any
                if (!isValid) {
                    e.preventDefault();
                    displayErrors(errors);
                    return;
                }

                // Disable submit button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            });

            function displayErrors(errors) {
                Object.keys(errors).forEach(field => {
                    const errorElement = document.getElementById(field + '-error');
                    const inputElement = document.getElementById(field);
                    
                    if (errorElement && inputElement) {
                        errorElement.textContent = errors[field];
                        errorElement.style.display = 'block';
                        inputElement.classList.add('error');
                    }
                });
            }

            function clearErrors() {
                const errorElements = document.querySelectorAll('.error-message');
                const inputElements = document.querySelectorAll('input, select');
                
                errorElements.forEach(element => {
                    element.style.display = 'none';
                });
                
                inputElements.forEach(element => {
                    element.classList.remove('error');
                });
            }
        });
    </script>
</body>
</html>
