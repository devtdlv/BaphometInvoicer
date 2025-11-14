@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div style="max-width: 600px; margin: 4rem auto; text-align: center;">
    <div class="card">
        <div style="font-size: 4rem; margin-bottom: 1rem;">✓</div>
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: var(--success);">Payment Successful</h1>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Your payment for invoice <strong>{{ $invoice->invoice_number }}</strong> has been processed successfully.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-primary">View Invoice</a>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back to Invoices</a>
        </div>
    </div>
</div>
@endsection

