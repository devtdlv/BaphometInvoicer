@extends('layouts.app')

@section('title', $client->name)

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">{{ $client->name }}</h1>
    <div style="display: flex; gap: 1rem;">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary">Edit</a>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Contact Information</h2>
    
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
        <div>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Email</p>
            <p style="font-weight: 600;">{{ $client->email }}</p>
        </div>
        
        @if($client->phone)
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Phone</p>
                <p style="font-weight: 600;">{{ $client->phone }}</p>
            </div>
        @endif
        
        @if($client->company)
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Company</p>
                <p style="font-weight: 600;">{{ $client->company }}</p>
            </div>
        @endif
        
        @if($client->tax_id)
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Tax ID</p>
                <p style="font-weight: 600;">{{ $client->tax_id }}</p>
            </div>
        @endif
    </div>
    
    @if($client->address_line_1)
        <div style="margin-top: 1.5rem;">
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Address</p>
            <p style="font-weight: 600;">
                {{ $client->address_line_1 }}<br>
                @if($client->address_line_2){{ $client->address_line_2 }}<br>@endif
                {{ $client->city }}{{ $client->state ? ', ' . $client->state : '' }} {{ $client->postal_code }}<br>
                {{ $client->country }}
            </p>
        </div>
    @endif
    
    @if($client->notes)
        <div style="margin-top: 1.5rem;">
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Notes</p>
            <p style="color: var(--text-secondary);">{{ $client->notes }}</p>
        </div>
    @endif
</div>

@if($client->invoices->count() > 0 || $client->quotes->count() > 0)
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">History</h2>
        
        @if($client->invoices->count() > 0)
            <h3 style="margin-bottom: 1rem; font-size: 1rem; font-weight: 600;">Invoices ({{ $client->invoices->count() }})</h3>
            <table class="table" style="margin-bottom: 2rem;">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->invoices->take(5) as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}" style="color: var(--accent);">{{ $invoice->invoice_number }}</a></td>
                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            <td>${{ number_format($invoice->total, 2) }}</td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($invoice->status === 'sent')
                                    <span class="badge badge-warning">Sent</span>
                                @else
                                    <span class="badge badge-info">Draft</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
        @if($client->quotes->count() > 0)
            <h3 style="margin-bottom: 1rem; font-size: 1rem; font-weight: 600;">Quotes ({{ $client->quotes->count() }})</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Quote #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($client->quotes->take(5) as $quote)
                        <tr>
                            <td><a href="{{ route('quotes.show', $quote) }}" style="color: var(--accent);">{{ $quote->quote_number }}</a></td>
                            <td>{{ $quote->issue_date->format('M d, Y') }}</td>
                            <td>${{ number_format($quote->total, 2) }}</td>
                            <td>
                                @if($quote->status === 'accepted')
                                    <span class="badge badge-success">Accepted</span>
                                @elseif($quote->status === 'sent')
                                    <span class="badge badge-warning">Sent</span>
                                @else
                                    <span class="badge badge-info">Draft</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endif
@endsection

