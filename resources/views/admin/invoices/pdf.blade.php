<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .invoice-box {
            width: 100%;
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .col {
            width: 48%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #eee;
        }

        .text-end {
            text-align: right;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .small {
            font-size: 12px;
        }

        .notes,
        .gateway {
            margin-top: 25px;
        }

        pre {
            background: #f5f5f5;
            padding: 10px;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Invoice</h2>
        <p>#{{ $invoice->invoice_number }}</p>
    </div>

    <div class="invoice-box">
        <div class="row mb-3">
            <div class="col">
                <p class="fw-bold mb-2">Billed To:</p>
                <p>{{ $invoice->user->name ?? '-' }}</p>
                <p class="small">{{ $invoice->user->email ?? '-' }}</p>
            </div>
            <div class="col text-end">
                <p class="fw-bold mb-2">Invoice Info:</p>
                <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                <p><strong>Due:</strong> {{ $invoice->formatted_due_date ?? '-' }}</p>
                <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->title }}</strong><br>
                            <small>{{ $item->description }}</small>
                        </td>
                        <td class="text-end">
                            {{ strtoupper($invoice->currency) . ' ' . number_format($item->amount / 100, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Subtotal</th>
                    <td class="text-end">{{ $invoice->formatted_total }}</td>
                </tr>
                @if ($invoice->tax_amount)
                    <tr>
                        <th>Tax</th>
                        <td class="text-end">{{ $invoice->formatted_tax }}</td>
                    </tr>
                @endif
                @if ($invoice->discount_amount)
                    <tr>
                        <th>Discount</th>
                        <td class="text-end">-{{ $invoice->formatted_discount }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Total</th>
                    <td class="text-end fw-bold">{{ $invoice->formatted_total }}</td>
                </tr>
            </tfoot>
        </table>

        @if ($invoice->notes)
            <div class="notes">
                <h4>Notes</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        @if ($invoice->gateway_response)
            <div class="gateway">
                <h4>Gateway Response</h4>
                <pre>{{ json_encode($invoice->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>

</body>

</html>
