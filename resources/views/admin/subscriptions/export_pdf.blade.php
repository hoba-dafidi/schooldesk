<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Subscriptions Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <h2>Subscriptions Export</h2>
    <table>
        <thead>
            <tr>
                <th>User Name</th>
                <th>User Email</th>
                <th>Plan</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Gateway</th>
                <th>Starts At</th>
                <th>Ends At</th>
                <th>Renews At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->user->email }}</td>
                    <td>{{ $subscription->plan->name }}</td>
                    <td>{{ ucfirst($subscription->status) }}</td>
                    <td>{{ $subscription->quantity }}</td>
                    <td>{{ $subscription->paymentGateway->name ?? 'Manual' }}</td>
                    <td>{{ $subscription->starts_at?->format('d M, Y H:i') ?? '-' }}</td>
                    <td>{{ $subscription->ends_at?->format('d M, Y H:i') ?? '-' }}</td>
                    <td>{{ $subscription->renews_at?->format('d M, Y H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Export generated at: {{ now()->toDayDateTimeString() }}</p>
</body>

</html>
