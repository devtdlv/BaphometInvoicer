@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Invoice {{ $invoice->invoice_number }}</h1>
    <div style="display: flex; gap: 1rem;">
        @if($invoice->status === 'draft')
            <form method="POST" action="{{ route('invoices.send', $invoice) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">Send Invoice</button>
            </form>
        @endif
        @if($invoice->status !== 'paid')
            <a href="{{ route('payments.create', $invoice) }}" class="btn btn-primary">Pay Now</a>
        @endif
        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary">Download PDF</a>
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-secondary">Edit</a>
    </div>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h3 style="margin-bottom: 0.5rem; font-weight: 600;">From</h3>
            <p style="color: var(--text-secondary);">{{ auth()->user()->name }}</p>
            <p style="color: var(--text-secondary);">{{ auth()->user()->email }}</p>
        </div>
        <div>
            <h3 style="margin-bottom: 0.5rem; font-weight: 600;">To</h3>
            <p style="color: var(--text-secondary);">{{ $invoice->client->name }}</p>
            <p style="color: var(--text-secondary);">{{ $invoice->client->email }}</p>
            @if($invoice->client->company)
                <p style="color: var(--text-secondary);">{{ $invoice->client->company }}</p>
            @endif
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
        <div>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Status</p>
            <p style="font-weight: 600;">
                @if($invoice->status === 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif($invoice->status === 'sent')
                    <span class="badge badge-warning">Sent</span>
                @elseif($invoice->status === 'overdue')
                    <span class="badge badge-danger">Overdue</span>
                @else
                    <span class="badge badge-info">Draft</span>
                @endif
            </p>
        </div>
        <div>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Issue Date</p>
            <p style="font-weight: 600;">{{ $invoice->issue_date->format('M d, Y') }}</p>
        </div>
        <div>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Due Date</p>
            <p style="font-weight: 600;">{{ $invoice->due_date->format('M d, Y') }}</p>
        </div>
        <div>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Total</p>
            <p style="font-weight: 600; font-size: 1.25rem; color: var(--accent);">
                {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }}
                <span style="font-size: 0.85rem; color: var(--text-secondary);">{{ $invoice->currency_code }}</span>
            </p>
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $invoice->currency_symbol }}{{ number_format($item->price, 2) }}</td>
                    <td>{{ $invoice->currency_symbol }}{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="border-top: 2px solid var(--border);">
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 600;">Subtotal:</td>
                <td style="font-weight: 600;">{{ $invoice->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600;">Discount:</td>
                    <td style="font-weight: 600; color: var(--success);">-{{ $invoice->currency_symbol }}{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600;">Tax ({{ $invoice->tax_rate }}%):</td>
                    <td style="font-weight: 600;">{{ $invoice->currency_symbol }}{{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 700; font-size: 1.125rem;">Total:</td>
                <td style="font-weight: 700; font-size: 1.125rem; color: var(--accent);">
                    {{ $invoice->currency_symbol }}{{ number_format($invoice->total, 2) }} {{ $invoice->currency_code }}
                </td>
            </tr>
        </tfoot>
    </table>
    
    @if($invoice->notes)
        <div style="margin-top: 2rem;">
            <h3 style="margin-bottom: 0.5rem; font-weight: 600;">Notes</h3>
            <p style="color: var(--text-secondary);">{{ $invoice->notes }}</p>
        </div>
    @endif
    
    @if($invoice->terms)
        <div style="margin-top: 1.5rem;">
            <h3 style="margin-bottom: 0.5rem; font-weight: 600;">Terms</h3>
            <p style="color: var(--text-secondary);">{{ $invoice->terms }}</p>
        </div>
    @endif

    @if($invoice->attachments->count())
        <div style="margin-top: 1.5rem;">
            <h3 style="margin-bottom: 0.5rem; font-weight: 600;">Attachments</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($invoice->attachments as $attachment)
                    <li style="margin-bottom: 0.5rem;">
                        <a href="{{ route('invoices.attachments.download', [$invoice, $attachment]) }}" style="color: var(--accent); text-decoration: none;">
                            {{ $attachment->original_name }}
                        </a>
                        <small style="color: var(--text-secondary);">
                            ({{ number_format($attachment->file_size / 1024, 1) }} KB)
                        </small>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@if($invoice->payments->count() > 0)
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Payment History</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->paid_at->format('M d, Y H:i') }}</td>
                        <td>{{ $invoice->currency_symbol }}{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td>
                            @if($payment->status === 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

