@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Invoices</h1>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">Create Invoice</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->client->name }}</td>
                    <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                    <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                    <td>${{ number_format($invoice->total, 2) }}</td>
                    <td>
                        @if($invoice->status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($invoice->status === 'sent')
                            <span class="badge badge-warning">Sent</span>
                        @elseif($invoice->status === 'overdue')
                            <span class="badge badge-danger">Overdue</span>
                        @else
                            <span class="badge badge-info">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">View</a>
                            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">PDF</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        No invoices found. <a href="{{ route('invoices.create') }}" style="color: var(--accent);">Create your first invoice</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 1.5rem;">
        {{ $invoices->links() }}
    </div>
</div>
@endsection

