<!DOCTYPE html>
<html>

<head>
    <title>Invoice PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <h2>Invoice #{{ $invoices->first()->invoice_number }}</h2>

    <p><strong>Customer:</strong> {{ $invoices->first()->user->name }} ({{ $invoices->first()->user->email }})</p>
    <p><strong>Status:</strong> {{ ucfirst($invoices->first()->status) }}</p>
    <p><strong>Due:</strong> {{ optional($invoices->first()->due_date)->format('F j, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices->first()->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->formatted_unit_price }}</td>
                    <td>{{ $item->formatted_total_price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: right; font-size: 14px; margin-top: 20px;">
        <strong>Total:</strong> {{ $invoices->first()->formatted_total }}
    </p>
</body>

</html>
