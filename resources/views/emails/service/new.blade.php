<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Service Added</title>
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
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
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
            border-left: 4px solid #ffc107;
        }
        .laundry-details {
            background: #fff3cd;
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
        <h1>🆕 New Service Added!</h1>
        <p>A new service has been submitted and requires your review</p>
    </div>

    <div class="content">
        <h2>Hello {{ $recipient->name }},</h2>
        
        <p>A new service has been added to the platform and requires your review and approval. Please review the service details below.</p>

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
            <p><strong>Added At:</strong> {{ $data['added_at'] }}</p>
            <p><strong>Service ID:</strong> #{{ $data['service_id'] }}</p>
        </div>

        <div class="laundry-details">
            <h3>Laundry Information:</h3>
            <p><strong>Laundry Name:</strong> {{ $data['laundry_name'] }}</p>
            <p><strong>Email:</strong> {{ $data['laundry_email'] }}</p>
            @if($data['laundry_phone'])
                <p><strong>Phone:</strong> {{ $data['laundry_phone'] }}</p>
            @endif
        </div>

        <p>Please review this service and take appropriate action:</p>
        <ul>
            <li>Check if the service complies with platform guidelines</li>
            <li>Verify pricing and coin costs are reasonable</li>
            <li>Ensure service description is clear and accurate</li>
            <li>Approve or reject the service based on your assessment</li>
        </ul>

        <p>This service will remain pending until you take action. Timely review helps maintain platform quality and supports our laundry partners.</p>

        <p>Best regards,<br>
        <strong>Laundry Service Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Laundry Service. All rights reserved.</p>
    </div>
</body>
</html>
