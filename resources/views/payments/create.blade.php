@extends('layouts.app')

@section('title', 'Pay Invoice')

@section('content')
<div style="max-width: 600px; margin: 4rem auto;">
    <div class="card">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">Pay Invoice</h1>
        
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Invoice Number</p>
            <p style="font-weight: 600; font-size: 1.125rem;">{{ $invoice->invoice_number }}</p>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 1rem; margin-bottom: 0.25rem;">Amount Due</p>
            <p style="font-weight: 700; font-size: 1.5rem; color: var(--accent);">${{ number_format($invoice->total, 2) }}</p>
        </div>
        
        <form method="POST" action="{{ route('payments.store', $invoice) }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Payment Gateway</label>
                <select name="gateway" class="form-input" required>
                    <option value="stripe">Stripe</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-input" value="{{ $invoice->total }}" step="0.01" min="0.01" max="{{ $invoice->total }}" required>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Proceed to Payment</button>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

