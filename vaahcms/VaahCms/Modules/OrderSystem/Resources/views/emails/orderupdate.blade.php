<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Update</title>
</head>
<body style="background-color:#f9fafb; font-family:Arial, sans-serif; padding:2rem;">
    <table style="width:100%; max-width:600px; margin:0 auto; background-color:#ffffff; border-radius:8px; padding:2rem;">
        <tr>
            <td>
                <h1 style="color:#3b82f6;">Order Update Notification</h1>
                <p>Hi {{ $order->customer->name ?? 'Customer' }},</p>
                <p>Your order <strong>{{ $order->slug }}</strong> has been updated. Below are the changes:</p>

                <table style="width:100%; border-collapse:collapse; margin-top:1rem;">
                    <thead>
                        <tr style="background-color:#f3f4f6;">
                            <th style="padding:8px; border:1px solid #ddd;">Field</th>
                            <th style="padding:8px; border:1px solid #ddd;">Old Value</th>
                            <th style="padding:8px; border:1px solid #ddd;">New Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($changes as $field => $val)
                            <tr>
                                <td style="padding:8px; border:1px solid #ddd;">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $val['old'] }}</td>
                                <td style="padding:8px; border:1px solid #ddd;">{{ $val['new'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p style="margin-top:2rem;">Thank you,<br>Support Team</p>
            </td>
        </tr>
    </table>
</body>
</html>
