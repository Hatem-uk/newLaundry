<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Received</title>
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
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
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
            border-left: 4px solid #4ecdc4;
        }
        .highlight {
            color: #4ecdc4;
            font-weight: bold;
        }
        .gift-box {
            background: #fff3cd;
            border-left-color: #ffc107;
            text-align: center;
        }
        .coins-box {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            text-align: center;
        }
        .cta-button {
            display: inline-block;
            background: #4ecdc4;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 25px;
            margin: 10px 0;
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
        <h1>🎁 You've Received a Gift!</h1>
        <p>Someone has gifted you a coin package</p>
    </div>
    
    <div class="content">
        <div class="info-box gift-box">
            <h2>🎉 Congratulations!</h2>
            <p><strong>{{ $sender_name }}</strong> has gifted you a coin package!</p>
            <p>This is a wonderful gesture from someone who cares about you.</p>
        </div>
        
        <div class="info-box">
            <h3>📦 Gift Details</h3>
            <p><strong>Package Name:</strong> <span class="highlight">{{ $package_name }}</span></p>
            <p><strong>Coins Received:</strong> <span class="highlight">{{ $coins_amount }} coins</span></p>
            <p><strong>Gift Date:</strong> {{ $gift_date }}</p>
            <p><strong>From:</strong> {{ $sender_name }} ({{ $sender_email }})</p>
        </div>
        
        <div class="info-box coins-box">
            <h3>💰 Your New Coin Balance</h3>
            <p>You now have <span class="highlight">{{ $coins_amount }} coins</span> added to your account!</p>
            <p>These coins can be used to purchase laundry services from our partner laundries.</p>
        </div>
        
        <div class="info-box">
            <h3>🚀 What You Can Do Now</h3>
            <ul>
                <li>Browse available laundry services</li>
                <li>Find laundries near your location</li>
                <li>Book services using your new coins</li>
                <li>Enjoy premium laundry services</li>
            </ul>
        </div>
        
        <div class="info-box" style="text-align: center;">
            <h3>🛒 Ready to Use Your Coins?</h3>
            <p>Start exploring our services and find the perfect laundry for your needs!</p>
            <a href="#" class="cta-button">Browse Services</a>
        </div>
        
        <div class="info-box">
            <h3>💡 Tips for Using Coins</h3>
            <ul>
                <li>Check service costs before booking</li>
                <li>Read laundry reviews and ratings</li>
                <li>Compare prices between different laundries</li>
                <li>Save coins for premium services</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p>Thank you for using our Laundry Service Platform!</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
