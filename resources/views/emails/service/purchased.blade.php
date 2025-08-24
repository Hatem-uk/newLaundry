<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Service Purchase</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-left: 4px solid #28a745;
        }
        .customer-details {
            background: #e8f5e8;
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
        <h1>🛒 New Service Purchase!</h1>
        <p>You have received a new order from a customer</p>
    </div>

    <div class="content">
        <h2>Hello {{ $recipient->name }},</h2>
        
        <p>Great news! You have received a new service purchase. Please process this order as soon as possible to maintain excellent customer satisfaction.</p>

        <div class="order-details">
            <h3>Order Details:</h3>
            <p><strong>Order ID:</strong> #{{ $data['order_id'] }}</p>
            <p><strong>Service Name:</strong> {{ $data['service_name'] }}</p>
            <p><strong>Order Amount:</strong> SAR {{ number_format($data['order_amount'], 2) }}</p>
            @if($data['coins_used'] > 0)
                <p><strong>Coins Used:</strong> {{ $data['coins_used'] }} coins</p>
            @endif
            <p><strong>Order Date:</strong> {{ $data['order_date'] }}</p>
            <p><strong>Status:</strong> {{ ucfirst($data['order_status']) }}</p>
        </div>

        <div class="customer-details">
            <h3>Customer Information:</h3>
            <p><strong>Name:</strong> {{ $data['customer_name'] }}</p>
            <p><strong>Email:</strong> {{ $data['customer_email'] }}</p>
            @if($data['customer_phone'])
                <p><strong>Phone:</strong> {{ $data['customer_phone'] }}</p>
            @endif
        </div>

        <p>Please take the following actions:</p>
        <ul>
            <li>Review the order details carefully</li>
            <li>Contact the customer if you need additional information</li>
            <li>Process the order according to your service standards</li>
            <li>Update the order status in your dashboard</li>
            <li>Ensure timely delivery or pickup as agreed</li>
        </ul>

        <p>This order represents a new revenue opportunity for your business. Providing excellent service will encourage repeat business and positive reviews.</p>

        <p>If you have any questions about this order, please contact our support team.</p>

        <p>Best regards,<br>
        <strong>Laundry Service Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Laundry Service. All rights reserved.</p>
    </div>
</body>
</html>
