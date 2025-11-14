<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 2px solid #8b5cf6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #8b5cf6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0; color: #8b5cf6;">Invoice {{ $invoice->invoice_number }}</h1>
        </div>

        <p>Dear {{ $invoice->client->name }},</p>

        <p>We've issued invoice <strong>{{ $invoice->invoice_number }}</strong> for the amount of <strong>{{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }} {{ $invoice->currency_code }}</strong>.</p>

        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('F d, Y') }}</p>

        <p>You can view and pay this invoice online by clicking the button below:</p>

        <a href="{{ route('portal.invoice', $invoice) }}" class="button">View & Pay Invoice</a>

        @if($invoice->notes)
            <div style="margin: 20px 0; padding: 15px; background: #f9f9f9; border-radius: 4px;">
                <strong>Notes:</strong><br>
                {{ $invoice->notes }}
            </div>
        @endif

        <p>If you have any questions, please don't hesitate to contact us.</p>

        <p>Best regards,<br>{{ auth()->user()->name }}</p>

        <div class="footer">
            <p>This is an automated email from {{ config('app.name') }}.</p>
        </div>
    </div>
</body>
</html>

