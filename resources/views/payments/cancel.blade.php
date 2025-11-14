@extends('layouts.app')

@section('title', 'Payment Cancelled')

@section('content')
<div style="max-width: 600px; margin: 4rem auto; text-align: center;">
    <div class="card">
        <div style="font-size: 4rem; margin-bottom: 1rem;">✗</div>
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: var(--danger);">Payment Cancelled</h1>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Your payment for invoice <strong>{{ $invoice->invoice_number }}</strong> was cancelled.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('payments.create', $invoice) }}" class="btn btn-primary">Try Again</a>
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">View Invoice</a>
        </div>
    </div>
</div>
@endsection

