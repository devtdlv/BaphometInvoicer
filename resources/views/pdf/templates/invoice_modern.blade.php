<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            color: #0f172a;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 40px;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .brand {
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-weight: 600;
            color: #6366f1;
        }
        .invoice-number {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead th {
            text-align: left;
            font-size: 11px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        table tbody td {
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-line {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }
        .total-line div {
            width: 200px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #475569;
        }
        .grand-total {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        .badge-paid { background: rgba(16, 185, 129, 0.2); color: #047857; }
        .badge-sent { background: rgba(245, 158, 11, 0.2); color: #92400e; }
        .badge-draft { background: rgba(99, 102, 241, 0.15); color: #3730a3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div>
                    <div class="brand">{{ config('app.name') }}</div>
                    <div class="invoice-number">Invoice {{ $invoice->invoice_number }}</div>
                    <div style="margin-top: 10px;">
                        @if($invoice->status === 'paid')
                            <span class="badge badge-paid">Paid</span>
                        @elseif($invoice->status === 'sent')
                            <span class="badge badge-sent">Sent</span>
                        @else
                            <span class="badge badge-draft">Draft</span>
                        @endif
                    </div>
                </div>
                <div style="text-align: right; color: #475569;">
                    <div>Issue Date: {{ $invoice->issue_date->format('F d, Y') }}</div>
                    <div>Due Date: {{ $invoice->due_date->format('F d, Y') }}</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 32px;">
                <div class="section">
                    <div class="section-title">From</div>
                    <div style="color: #0f172a; font-weight: 600;">{{ $invoice->user->name }}</div>
                    <div style="color: #475569;">{{ $invoice->user->email }}</div>
                </div>
                <div class="section">
                    <div class="section-title">Bill To</div>
                    <div style="color: #0f172a; font-weight: 600;">{{ $invoice->client->name }}</div>
                    <div style="color: #475569;">{{ $invoice->client->email }}</div>
                    @if($invoice->client->company)
                        <div style="color: #475569;">{{ $invoice->client->company }}</div>
                    @endif
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Quantity</th>
                        <th style="text-align: right;">Rate</th>
                        <th style="text-align: right;">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <td style="color: #0f172a; font-weight: 600;">
                                {{ $item->description }}
                            </td>
                            <td style="text-align: right;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">{{ $invoice->currency_symbol }}{{ number_format($item->price, 2) }}</td>
                            <td style="text-align: right;">{{ $invoice->currency_symbol }}{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 30px;">
                <div class="total-line">
                    <div>
                        <span>Subtotal</span>
                        <span>{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="total-line">
                        <div>
                            <span>Discount</span>
                            <span>-{{ $invoice->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                    </div>
                @endif
                @if($invoice->tax_amount > 0)
                    <div class="total-line">
                        <div>
                            <span>Tax ({{ $invoice->tax_rate }}%)</span>
                            <span>{{ $invoice->currency_symbol }}{{ number_format($invoice->tax_amount, 2) }}</span>
                        </div>
                    </div>
                @endif
                <div class="total-line">
                    <div class="grand-total">
                        <span>Total</span>
                        <span>{{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }} {{ $invoice->currency_code }}</span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
                <div class="section" style="margin-top: 30px;">
                    <div class="section-title">Notes</div>
                    <div style="color: #475569;">{{ $invoice->notes }}</div>
                </div>
            @endif

            @if($invoice->terms)
                <div class="section">
                    <div class="section-title">Terms</div>
                    <div style="color: #475569;">{{ $invoice->terms }}</div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>

