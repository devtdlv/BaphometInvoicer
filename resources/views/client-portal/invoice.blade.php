@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Invoice {{ $invoice->invoice_number }}</h1>
    <div style="display: flex; gap: 1rem;">
        @if($invoice->status !== 'paid')
            <a href="{{ route('payments.create', $invoice) }}" class="btn btn-primary">Pay Now</a>
        @endif
        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary">Download PDF</a>
    </div>
</div>

<div class="card">
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h3 style="margin-bottom: 0.5rem; font-weight: 600;">From</h3>
            <p style="color: var(--text-secondary);">{{ $invoice->user->name }}</p>
            <p style="color: var(--text-secondary);">{{ $invoice->user->email }}</p>
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
            <p style="font-weight: 600; font-size: 1.25rem; color: var(--accent);">${{ number_format($invoice->total, 2) }}</p>
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
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="border-top: 2px solid var(--border);">
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 600;">Subtotal:</td>
                <td style="font-weight: 600;">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600;">Discount:</td>
                    <td style="font-weight: 600; color: var(--success);">-${{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
            @endif
            @if($invoice->tax_amount > 0)
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: 600;">Tax ({{ $invoice->tax_rate }}%):</td>
                    <td style="font-weight: 600;">${{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 700; font-size: 1.125rem;">Total:</td>
                <td style="font-weight: 700; font-size: 1.125rem; color: var(--accent);">${{ number_format($invoice->total, 2) }}</td>
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
</div>
@endsection

