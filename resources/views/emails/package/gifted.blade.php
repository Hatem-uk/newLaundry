<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Gifted</title>
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
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
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
            border-left: 4px solid #ff6b6b;
        }
        .highlight {
            color: #ff6b6b;
            font-weight: bold;
        }
        .sender-info {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .recipient-info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
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
        <h1>🎁 Package Gifted Successfully!</h1>
        <p>A customer has gifted a coin package to another user</p>
    </div>
    
    <div class="content">
        <h2>Package Gift Details</h2>
        
        <div class="info-box">
            <p><strong>Order ID:</strong> <span class="highlight">#{{ $order_id }}</span></p>
            <p><strong>Package Name:</strong> <span class="highlight">{{ $package_name }}</span></p>
            <p><strong>Package Price:</strong> ${{ $package_price }}</p>
            <p><strong>Coins Amount:</strong> {{ $coins_amount }} coins</p>
            <p><strong>Gift Date:</strong> {{ $gift_date }}</p>
            <p><strong>Status:</strong> <span class="highlight">{{ ucfirst($order_status) }}</span></p>
        </div>
        
        <div class="info-box sender-info">
            <h3>👤 Sender Information</h3>
            <p><strong>Name:</strong> {{ $sender_name }}</p>
            <p><strong>Email:</strong> {{ $sender_email }}</p>
            <p>The sender has purchased and gifted this package to another user.</p>
        </div>
        
        <div class="info-box recipient-info">
            <h3>🎯 Recipient Information</h3>
            <p><strong>Name:</strong> {{ $recipient_name }}</p>
            <p><strong>Email:</strong> {{ $recipient_email }}</p>
            <p>The recipient will receive {{ $coins_amount }} coins to their account.</p>
        </div>
        
        <div class="info-box">
            <h3>📊 Summary</h3>
            <p>A package gift transaction has been completed successfully. This demonstrates customer engagement and can lead to:</p>
            <ul>
                <li>Increased customer retention</li>
                <li>Word-of-mouth marketing</li>
                <li>Higher platform usage</li>
                <li>Potential new customer acquisition</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>🔍 Next Steps</h3>
            <ul>
                <li>Monitor recipient's usage of gifted coins</li>
                <li>Track if recipient becomes an active user</li>
                <li>Consider gifting promotion campaigns</li>
                <li>Analyze gifting patterns for marketing insights</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from the Laundry Service Platform</p>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
