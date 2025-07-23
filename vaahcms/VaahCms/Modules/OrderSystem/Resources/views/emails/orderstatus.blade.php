<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="background-color:#f9fafb; font-family:Arial, sans-serif; padding:2rem; margin:0;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; padding:2rem; box-shadow:0 0 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="padding-bottom:1rem;">
                            <h1 style="font-size:1.5rem; font-weight:600; color:#3b82f6; margin:0;">Order Confirmation</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:1rem;">Hi {{ $order->customer->name ?? 'Customer' }},</td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:1rem;">Your order has been successfully placed. Below are your order details:</td>
                    </tr>
                    <tr><td><strong>Order Slug:</strong> {{ $order->slug }}</td></tr>
                    <tr><td><strong>Total Quantity:</strong> {{ $order->total_quantity }}</td></tr>
                    <tr><td><strong>Total Price:</strong> ₹{{ number_format($order->total_price, 2) }}</td></tr>
                    <tr><td><strong>Status:</strong> {{ $order->status->name ?? 'Unknown' }}</td></tr>
                    <tr><td><strong>Active:</strong> {{ $order->is_active ? 'Yes' : 'No' }}</td></tr>

                    <tr>
                        <td style="padding-top:1.5rem;">
                            <h3 style="margin-bottom:0.5rem;">Ordered Products:</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <thead>
                                    <tr style="background-color:#f3f4f6;">
                                        <th style="padding:8px; border:1px solid #ddd; text-align:left;">Product</th>
                                        <th style="padding:8px; border:1px solid #ddd; text-align:left;">Quantity</th>
                                        <th style="padding:8px; border:1px solid #ddd; text-align:left;">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->products as $product)
                                        <tr>
                                            <td style="padding:8px; border:1px solid #ddd;">{{ $product->name }}</td>
                                            <td style="padding:8px; border:1px solid #ddd;">{{ $product->pivot->quantity }}</td>
                                            <td style="padding:8px; border:1px solid #ddd;">₹{{ number_format($product->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <tr><td style="padding-top:2rem;">Thank you for shopping with us!</td></tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
