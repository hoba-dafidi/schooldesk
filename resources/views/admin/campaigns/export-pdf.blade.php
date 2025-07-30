<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Affiliate Campaigns Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h2>Affiliate Campaigns Report</h2>
    <table>
        <thead>
            <tr>
                <th>Campaign Name</th>
                <th>Affiliate</th>
                <th>Code</th>
                <th>Clicks</th>
                <th>Signups</th>
                <th>Commission Rate</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $campaign)
                <tr>
                    <td>{{ $campaign->name }}</td>
                    <td>{{ $campaign->affiliate->user->name ?? '—' }}</td>
                    <td>{{ $campaign->code }}</td>
                    <td>{{ $campaign->clicks }}</td>
                    <td>{{ $campaign->unique_signups }}</td>
                    <td>{{ $campaign->custom_commission_rate ? $campaign->custom_commission_rate . '%' : '—' }}</td>
                    <td>{{ $campaign->isActive() ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $campaign->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
