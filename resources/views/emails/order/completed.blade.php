<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Completed</title>
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
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
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
        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .participants {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
        <h1>✅ Order Completed!</h1>
        <p>A service order has been successfully completed</p>
    </div>

    <div class="content">
        <h2>Hello {{ $recipient->name }},</h2>
        
        <p>Great news! A service order has been completed successfully. This represents a successful transaction on our platform.</p>

        <div class="order-details">
            <h3>Order Details:</h3>
            <p><strong>Order ID:</strong> #{{ $data['order_id'] }}</p>
            <p><strong>Service Name:</strong> {{ $data['service_name'] }}</p>
            <p><strong>Order Amount:</strong> SAR {{ number_format($data['order_amount'], 2) }}</p>
            @if($data['coins_used'] > 0)
                <p><strong>Coins Used:</strong> {{ $data['coins_used'] }} coins</p>
            @endif
            <p><strong>Order Date:</strong> {{ $data['order_date'] }}</p>
            <p><strong>Completed At:</strong> {{ $data['completed_at'] }}</p>
            <p><strong>Status:</strong> {{ ucfirst($data['order_status']) }}</p>
        </div>

        <div class="participants">
            <h3>Transaction Participants:</h3>
            <p><strong>Laundry:</strong> {{ $data['laundry_name'] }}</p>
            <p><strong>Customer:</strong> {{ $data['customer_name'] }}</p>
            <p><strong>Customer Email:</strong> {{ $data['customer_email'] }}</p>
        </div>

        <p>This completed order contributes to:</p>
        <ul>
            <li>Platform revenue and growth</li>
            <li>Laundry business success</li>
            <li>Customer satisfaction and retention</li>
            <li>Overall platform reputation</li>
        </ul>

        <p>The order completion indicates that both the laundry and customer were satisfied with the transaction. This is a positive indicator of platform health and service quality.</p>

        <p>Best regards,<br>
        <strong>Laundry Service Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Laundry Service. All rights reserved.</p>
    </div>
</body>
</html>
