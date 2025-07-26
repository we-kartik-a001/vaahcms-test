<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Our Newsletter</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .welcome-icon {
            font-size: 48px;
            color: #3498db;
            margin-bottom: 15px;
        }
        .content {
            padding: 0 15px;
        }
        .highlight {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }
        .email-address {
            font-weight: bold;
            color: #2980b9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
        }
        .signature {
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="welcome-icon">✉️</div>
            <h2>Welcome to Our Newsletter!</h2>
        </div>
        
        <div class="content">
            <p>We're thrilled to have you join our community!</p>
            
            <div class="highlight">
                <p>You've successfully subscribed with: <span class="email-address">{{ $email->email }}</span></p>
            </div>
            
            <p>Get ready to receive:</p>
            <ul>
                <li>Exclusive content and articles</li>
                <li>Latest updates from our blog</li>
                <li>Special offers and announcements</li>
            </ul>
            
            <p>We promise to only send you valuable content you'll love.</p>
        </div>
        
        <div class="footer">
            <p class="signature">— The Blog System Team</p>
            <p><small>You can unsubscribe anytime by clicking the link in our emails</small></p>
        </div>
    </div>
</body>
</html>