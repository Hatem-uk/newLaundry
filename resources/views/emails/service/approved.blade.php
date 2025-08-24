<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Approved</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .service-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .status-badge {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Service Approved!</h1>
        <p>Your service has been successfully approved by our admin team</p>
    </div>

    <div class="content">
        <h2>Hello {{ $recipient->name }},</h2>
        
        <p>Great news! Your service has been approved and is now live on our platform. Customers can now view and purchase your service.</p>

        <div class="service-details">
            <h3>Service Details:</h3>
            <p><strong>Service Name:</strong> {{ $data['service_name'] }}</p>
            <p><strong>Description:</strong> {{ $data['service_description'] }}</p>
            <p><strong>Type:</strong> {{ ucfirst($data['service_type']) }}</p>
            @if($data['coin_cost'])
                <p><strong>Coin Cost:</strong> {{ $data['coin_cost'] }} coins</p>
            @endif
            @if($data['price'])
                <p><strong>Price:</strong> SAR {{ number_format($data['price'], 2) }}</p>
            @endif
            <p><strong>Approved At:</strong> {{ $data['approved_at'] }}</p>
            <p><strong>Service ID:</strong> #{{ $data['service_id'] }}</p>
            
            <div style="margin-top: 20px;">
                <span class="status-badge">✅ APPROVED</span>
            </div>
        </div>

        <p>Your service is now visible to customers and can generate revenue for your business. Make sure to:</p>
        <ul>
            <li>Keep your service information up to date</li>
            <li>Maintain high-quality service delivery</li>
            <li>Respond promptly to customer orders</li>
            <li>Monitor your service performance</li>
        </ul>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
        <strong>Laundry Service Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Laundry Service. All rights reserved.</p>
    </div>
</body>
</html>
