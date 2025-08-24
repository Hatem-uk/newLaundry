<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Purchased</title>
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
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .highlight {
            color: #667eea;
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
        <h1>🎉 Package Purchased Successfully!</h1>
        <p>New package purchase has been completed</p>
    </div>
    
    <div class="content">
        <h2>Package Purchase Details</h2>
        
        <div class="info-box">
            <p><strong>Order ID:</strong> <span class="highlight">#{{ $order_id }}</span></p>
            <p><strong>Package Name:</strong> <span class="highlight">{{ $package_name }}</span></p>
            <p><strong>Customer Name:</strong> {{ $customer_name }}</p>
            <p><strong>Customer Email:</strong> {{ $customer_email }}</p>
            <p><strong>Package Price:</strong> ${{ $package_price }}</p>
            <p><strong>Coins Amount:</strong> {{ $coins_amount }} coins</p>
            <p><strong>Purchase Type:</strong> {{ ucfirst($payment_method) }}</p>
            <p><strong>Order Date:</strong> {{ $order_date }}</p>
            <p><strong>Status:</strong> <span class="highlight">{{ ucfirst($order_status) }}</span></p>
        </div>
        
        <div class="info-box">
            <h3>📊 Summary</h3>
            <p>A customer has successfully purchased a coin package. The customer now has access to {{ $coins_amount }} coins to spend on laundry services.</p>
            <p>This purchase contributes to the platform's revenue and provides value to the customer.</p>
        </div>
        
        <div class="info-box">
            <h3>🔍 Next Steps</h3>
            <ul>
                <li>Monitor the customer's usage of the purchased coins</li>
                <li>Track service purchases made with these coins</li>
                <li>Ensure proper coin balance management</li>
                <li>Consider follow-up marketing for additional packages</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from the Laundry Service Platform</p>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
