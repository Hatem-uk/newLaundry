<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Laundry Service System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2196F3;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .welcome-message {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .status-info {
            background-color: #e3f2fd;
            border: 1px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .next-steps {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            margin-top: 20px;
        }
        .action-btn {
            display: inline-block;
            background-color: #2196F3;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
        }
        .info-row {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 3px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Laundry Service System!</h1>
        <p>Thank you for joining our platform as an Agent</p>
    </div>

    <div class="content">
        <div class="welcome-message">
            <h2>Hello {{ $user_name }}!</h2>
            <p>Welcome to the Laundry Service System as an Agent. We're excited to have you on board!</p>
        </div>

        <h3>Your Account Details</h3>
        <div class="info-row">
            <span class="label">Agent Name:</span>
            <span class="value">{{ $agent_name }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Registration Date:</span>
            <span class="value">{{ $registration_date }}</span>
        </div>

        <div class="status-info">
            <h4>Account Status: <strong>{{ ucfirst($status) }}</strong></h4>
            <p>{{ $next_steps }}</p>
        </div>

        @if($status === 'pending')
        <div class="next-steps">
            <h4>What Happens Next?</h4>
            <ul>
                <li>Our admin team will review your registration</li>
                <li>You'll receive an email once your account is approved</li>
                <li>After approval, you can start offering agent services</li>
            </ul>
        </div>
        @else
        <div class="next-steps">
            <h4>Your Account is Ready!</h4>
            <ul>
                <li>Start offering your agent services</li>
                <li>Connect with laundries in your area</li>
                <li>Begin managing customer requests</li>
            </ul>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            @if($status === 'approved')
            <a href="{{ url('/agent/dashboard') }}" class="action-btn">Go to Dashboard</a>
            <a href="{{ url('/agent/services/create') }}" class="action-btn">Add Services</a>
            @else
            <a href="{{ url('/') }}" class="action-btn">Visit Our Website</a>
            @endif
        </div>

        <div style="background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4>Agent Benefits</h4>
            <ul>
                <li>Flexible working hours</li>
                <li>Commission-based earnings</li>
                <li>Access to multiple laundries</li>
                <li>Customer management tools</li>
            </ul>
        </div>

        <div style="background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4>Need Help?</h4>
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team:</p>
            <ul>
                <li>Email: support@laundrysystem.com</li>
                <li>Phone: +966-XX-XXX-XXXX</li>
                <li>Support Hours: Sunday - Thursday, 9:00 AM - 6:00 PM (KSA Time)</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing Laundry Service System</p>
        <p>This is an automated email - please do not reply</p>
    </div>
</body>
</html>
