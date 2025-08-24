<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Agent Registration</title>
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
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 3px;
            margin: 10px 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Agent Registration</h1>
        <p>A new agent has registered and requires approval</p>
    </div>

    <div class="content">
        <h2>Registration Details</h2>
        
        <div class="info-row">
            <span class="label">User Name:</span>
            <span class="value">{{ $user_name }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Email:</span>
            <span class="value">{{ $user_email }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Phone:</span>
            <span class="value">{{ $user_phone ?? 'Not provided' }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Agent Name:</span>
            <span class="value">{{ $agent_name }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Address:</span>
            <span class="value">{{ $agent_address ?? 'Not provided' }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Agent Phone:</span>
            <span class="value">{{ $agent_phone ?? 'Not provided' }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">Registration Date:</span>
            <span class="value">{{ $registration_date }}</span>
        </div>
        
        <div class="info-row">
            <span class="label">User ID:</span>
            <span class="value">{{ $user_id }}</span>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Action Required:</strong> Please review this registration and approve or reject the account.</p>
            
            <a href="{{ url('/admin/approvals') }}" class="action-btn">Review Approvals</a>
            <a href="{{ url('/admin/users/' . $user_id) }}" class="action-btn">View User Details</a>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from the Laundry Service System</p>
        <p>Please do not reply to this email</p>
    </div>
</body>
</html>
