<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Invoice #{{ $invoice->invoice_number }}</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f7f7f7;">
    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0; padding: 20px;">
        <tr>
            <td>
                <table align="center" width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; padding: 20px; border-radius: 8px;">
                    <tr>
                        <td style="text-align: center; padding-bottom: 20px;">
                            <h2 style="margin: 0;">Invoice</h2>
                            <p style="margin: 0;">#{{ $invoice->invoice_number }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p>Hi {{ $invoice->user->name ?? 'User' }},</p>
                            <p>Thank you for your payment. Here are the details of your invoice:</p>

                            <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Invoice Date:</strong></td>
                                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>{{ ucfirst($invoice->status) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td>{{ $invoice->formatted_total }}</td>
                                </tr>
                            </table>

                            @if ($invoice->items && count($invoice->items))
                                <h4 style="margin-top: 30px;">Invoice Items</h4>
                                <table width="100%" cellpadding="5" cellspacing="0"
                                    style="border: 1px solid #ddd; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background-color: #f1f1f1;">
                                            <th align="left" style="border: 1px solid #ddd;">Description</th>
                                            <th align="right" style="border: 1px solid #ddd;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->items as $item)
                                            <tr>
                                                <td style="border: 1px solid #ddd;">
                                                    <strong>{{ $item->title }}</strong><br>
                                                    <small>{{ $item->description }}</small>
                                                </td>
                                                <td align="right" style="border: 1px solid #ddd;">
                                                    {{ strtoupper($invoice->currency) . ' ' . number_format($item->amount / 100, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if ($invoice->notes)
                                <p style="margin-top: 20px;"><strong>Notes:</strong><br>{{ $invoice->notes }}</p>
                            @endif

                            <p style="margin-top: 30px;">
                                You can <a href="{{ $viewUrl }}" style="color: #0d6efd;">view or download your
                                    invoice</a> anytime.
                            </p>

                            <p>Thank you,<br>The {{ config('app.name') }} Team</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: center; font-size: 12px; color: #888888; padding-top: 30px;">
                            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
