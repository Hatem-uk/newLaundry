<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Laundry Service</title>
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
            max-width: 400px;
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

        .form-container {
            padding: 40px 30px;
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

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color:rgb(91, 121, 255);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input.error {
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
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .register-link {
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
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

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 50px;
        }

        .password-toggle .toggle-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 16px;
        }

        .password-toggle .toggle-btn:hover {
            color: #333;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-shield"></i> Admin Login</h1>
            <p>Access your admin dashboard</p>
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

            <form method="POST" action="{{ route('admin.login') }}" id="adminLoginForm">
                @csrf
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    <div class="error-message" id="email-error"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-toggle">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-btn" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="password-error"></div>
                </div>

                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

                <div class="register-link">
                    Don't have an account? <a href="{{ route('admin.register') }}">Register here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('adminLoginForm');
            const submitBtn = document.getElementById('submitBtn');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            // Password toggle functionality
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const errors = {};

                // Clear previous errors
                clearErrors();

                // Validate email
                const email = document.getElementById('email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    errors.email = 'Please enter a valid email address';
                    isValid = false;
                }

                // Validate password
                const password = document.getElementById('password').value;
                if (password.length < 1) {
                    errors.password = 'Password is required';
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
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
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
                const inputElements = document.querySelectorAll('input');
                
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
