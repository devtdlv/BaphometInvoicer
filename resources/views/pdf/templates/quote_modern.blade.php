<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 12px; color: #0f172a; background: #f8fafc; margin: 0; }
        .container { width: 100%; padding: 40px; }
        .card { background: #fff; border-radius: 16px; padding: 36px; box-shadow: 0 15px 45px rgba(15,23,42,0.08); }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
        .badge { padding: 6px 16px; border-radius: 999px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; }
        .badge-draft { background: rgba(99,102,241,.12); color: #4338ca; }
        .badge-sent { background: rgba(251,191,36,.2); color: #92400e; }
        .badge-accepted { background: rgba(16,185,129,.15); color: #065f46; }
        .badge-rejected { background: rgba(239,68,68,.15); color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th { text-align: left; font-size: 11px; letter-spacing: 0.1em; color: #94a3b8; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 14px 0; border-bottom: 1px solid #f1f5f9; }
        .totals { display: flex; justify-content: flex-end; margin-top: 20px; color: #475569; }
        .totals div { width: 240px; display: flex; justify-content: space-between; margin-bottom: 6px; }
        .grand { font-size: 20px; font-weight: 700; color: #111827; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div>
                    <div style="text-transform: uppercase; letter-spacing: 0.4em; font-size: 10px; color: #94a3b8;">{{ config('app.name') }}</div>
                    <h1 style="margin: 8px 0 0; font-size: 30px;">Quote {{ $quote->quote_number }}</h1>
                </div>
                <div>
                    @php
                        $badgeClass = match($quote->status) {
                            'accepted' => 'badge-accepted',
                            'sent' => 'badge-sent',
                            'rejected' => 'badge-rejected',
                            default => 'badge-draft',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ strtoupper($quote->status) }}</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px;">
                <div>
                    <p style="text-transform: uppercase; letter-spacing: 0.2em; font-size: 11px; color: #94a3b8;">From</p>
                    <p style="margin: 4px 0; font-weight: 600;">{{ $quote->user->name }}</p>
                    <p style="margin: 0; color: #475569;">{{ $quote->user->email }}</p>
                </div>
                <div>
                    <p style="text-transform: uppercase; letter-spacing: 0.2em; font-size: 11px; color: #94a3b8;">To</p>
                    <p style="margin: 4px 0; font-weight: 600;">{{ $quote->client->name }}</p>
                    <p style="margin: 0; color: #475569;">{{ $quote->client->email }}</p>
                    @if($quote->client->company)
                        <p style="margin: 0; color: #475569;">{{ $quote->client->company }}</p>
                    @endif
                </div>
            </div>

            <div style="margin-top: 20px; color: #475569;">
                <p><strong>Issue Date:</strong> {{ $quote->issue_date->format('F d, Y') }}</p>
                <p><strong>Valid Until:</strong> {{ $quote->expiry_date ? $quote->expiry_date->format('F d, Y') : 'No expiry' }}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Qty</th>
                        <th style="text-align: right;">Rate</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                        <tr>
                            <td style="font-weight: 600;">{{ $item->description }}</td>
                            <td style="text-align: right;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">{{ $quote->currency_symbol }}{{ number_format($item->price, 2) }}</td>
                            <td style="text-align: right;">{{ $quote->currency_symbol }}{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div>
                    <span>Subtotal</span>
                    <span>{{ $quote->currency_symbol }}{{ number_format($quote->subtotal, 2) }}</span>
                </div>
            </div>
            @if($quote->discount_amount > 0)
                <div class="totals">
                    <div>
                        <span>Discount</span>
                        <span>-{{ $quote->currency_symbol }}{{ number_format($quote->discount_amount, 2) }}</span>
                    </div>
                </div>
            @endif
            @if($quote->tax_amount > 0)
                <div class="totals">
                    <div>
                        <span>Tax ({{ $quote->tax_rate }}%)</span>
                        <span>{{ $quote->currency_symbol }}{{ number_format($quote->tax_amount, 2) }}</span>
                    </div>
                </div>
            @endif
            <div class="totals">
                <div class="grand">
                    <span>Total</span>
                    <span>{{ $quote->currency_symbol }}{{ number_format($quote->total, 2) }} {{ $quote->currency_code }}</span>
                </div>
            </div>

            @if($quote->notes)
                <div style="margin-top: 24px;">
                    <p style="text-transform: uppercase; letter-spacing: 0.2em; font-size: 11px; color: #94a3b8;">Notes</p>
                    <p style="color: #475569;">{{ $quote->notes }}</p>
                </div>
            @endif

            @if($quote->terms)
                <div style="margin-top: 12px;">
                    <p style="text-transform: uppercase; letter-spacing: 0.2em; font-size: 11px; color: #94a3b8;">Terms</p>
                    <p style="color: #475569;">{{ $quote->terms }}</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>

