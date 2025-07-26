<!DOCTYPE html>
<html>
<head>
    <title>New Blog Created</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        h2 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .blog-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .blog-info p {
            margin: 8px 0;
        }
        strong {
            color: #2c3e50;
            min-width: 100px;
            display: inline-block;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #7f8c8d;
        }
        .logo {
            color: #3498db;
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Blog Created</h2>
        
        <div class="blog-info">
            <p><strong>Title:</strong> {{ $item->name }}</p>
            <p><strong>Slug:</strong> {{ $item->slug }}</p>
            <p><strong>Description:</strong> {{ $item->description }}</p>
        </div>
        
        <div class="footer">
            <p>Thank you,<br><span class="logo">VaahCMS Blog System</span></p>
        </div>
    </div>
</body>
</html>