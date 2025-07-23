<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Order Deletion Notification</title>
    <style>
        table,
        th,
        td {
            border: 1px solid #ccc;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f7f7f7;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 650px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #8b0000;
            color: white;
            padding: 20px 30px;
            font-size: 20px;
            font-weight: bold;
        }

        .body {
            padding: 30px;
        }

        .footer {
            background-color: #eee;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #555;
        }

        .title {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .small-text {
            font-size: 13px;
            color: #666;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }

        .highlight {
            color: #8b0000;
            font-weight: bold;
        }
    </style>
</head>

<body>

    @php
        $isMultiple = is_iterable($collection) && count($collection) > 1;
        $records = $isMultiple ? $collection : [$collection];
        $first = $records[0] ?? null;

        $super_admin_name = trim(
            ($super_admin->first_name ?? '') . ' ' .
            ($super_admin->middle_name ?? '') . ' ' .
            ($super_admin->last_name ?? '')
        );
    @endphp

    <div class="container">
        <div class="header">
            Order Deletion Notification
        </div>

        <div class="body">
            <p>Hello {{ $super_admin_name ?: 'Admin' }},</p>

            @if($isMultiple)
                <p class="title">
                    The following <span class="highlight">{{ count($records) }}</span> orders have been deleted from the system:
                </p>
            @else
                <p class="title">An order has been deleted from the system:</p>
            @endif

            @if($first)
                <p class="small-text">
                    <strong>Deleted By:</strong> {{ $first->deletedByUser->name ?? 'System' }}<br>
                    <strong>Deleted At:</strong> {{ $first->deleted_at ?? now() }}
                </p>
            @endif

            <table width="100%" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Customer</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $order)
                        <tr>
                            <td>{{ $order->id ?? '—' }}</td>
                            <td>{{ $order->customer->name ?? '—' }}</td>
                            <td>₹ {{ number_format($order->total_price ?? 0, 2) }}</td>
                            <td>{{ $order->status->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="section-title">Please verify and log this activity for records.</p>

            <p class="small-text" style="margin-top: 30px;">
                — This is an automated message. Do not reply.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Order Management System. All rights reserved.
        </div>
    </div>

</body>

</html>
