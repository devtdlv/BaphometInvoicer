<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #0f172a;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f8fafc;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #5b21b6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <p style="text-transform: uppercase; letter-spacing: 0.2em; font-size: 12px; color: #94a3b8;">Payment Reminder</p>
        <h1 style="margin-top: 0;">Invoice {{ $invoice->invoice_number }}</h1>

        <p>Dear {{ $invoice->client->name }},</p>

        <p>This is a friendly reminder that invoice <strong>{{ $invoice->invoice_number }}</strong> for <strong>{{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }} {{ $invoice->currency_code }}</strong> is {{ $invoice->due_date->isPast() ? 'past due' : 'due soon' }}.</p>

        <ul style="list-style: none; padding: 0; color: #475569;">
            <li><strong>Due Date:</strong> {{ $invoice->due_date->format('F d, Y') }}</li>
            <li><strong>Status:</strong> {{ ucfirst($invoice->status) }}</li>
        </ul>

        <a href="{{ route('portal.invoice', $invoice) }}" class="button">View & Pay Invoice</a>

        <p>If you have already sent payment, please disregard this reminder.</p>

        <p>Thank you,<br>{{ $invoice->user->name }}</p>

        <div class="footer">
            Sent automatically from {{ config('app.name') }} • {{ config('app.url') }}
        </div>
    </div>
</body>
</html>

