<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .from-to {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .from, .to {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .to {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;">INVOICE</h1>
        <p style="margin: 5px 0; font-size: 18px; font-weight: bold;">{{ $invoice->invoice_number }}</p>
    </div>

    <div class="from-to">
        <div class="from">
            <strong>From:</strong><br>
            {{ $invoice->user->name }}<br>
            {{ $invoice->user->email }}
        </div>
        <div class="to">
            <strong>To:</strong><br>
            {{ $invoice->client->name }}<br>
            {{ $invoice->client->email }}<br>
            @if($invoice->client->company)
                {{ $invoice->client->company }}<br>
            @endif
        </div>
    </div>

    <div class="invoice-info">
        <p><strong>Issue Date:</strong> {{ $invoice->issue_date->format('F d, Y') }}</p>
        <p><strong>Due Date:</strong> {{ $invoice->due_date->format('F d, Y') }}</p>
        <p><strong>Status:</strong> {{ strtoupper($invoice->status) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td colspan="3" class="text-right"><strong>Discount:</strong></td>
                    <td class="text-right">-${{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr>
                    <td colspan="3" class="text-right"><strong>Tax ({{ $invoice->tax_rate }}%):</strong></td>
                    <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td colspan="3" class="text-right"><strong>TOTAL:</strong></td>
                <td class="text-right">${{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($invoice->notes)
        <div style="margin-bottom: 20px;">
            <strong>Notes:</strong><br>
            {{ $invoice->notes }}
        </div>
    @endif

    @if($invoice->terms)
        <div>
            <strong>Terms:</strong><br>
            {{ $invoice->terms }}
        </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>

