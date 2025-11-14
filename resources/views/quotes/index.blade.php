@extends('layouts.app')

@section('title', 'Quotes')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Quotes</h1>
    <a href="{{ route('quotes.create') }}" class="btn btn-primary">Create Quote</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Quote #</th>
                <th>Client</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotes as $quote)
                <tr>
                    <td>{{ $quote->quote_number }}</td>
                    <td>{{ $quote->client->name }}</td>
                    <td>{{ $quote->issue_date->format('M d, Y') }}</td>
                    <td>{{ $quote->expiry_date ? $quote->expiry_date->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        <strong>{{ $quote->currency_symbol }}{{ number_format($quote->total, 2) }}</strong>
                        <span style="color: var(--text-secondary); font-size: 0.8rem;">{{ $quote->currency_code }}</span>
                    </td>
                    <td>
                        @if($quote->status === 'accepted')
                            <span class="badge badge-success">Accepted</span>
                        @elseif($quote->status === 'sent')
                            <span class="badge badge-warning">Sent</span>
                        @elseif($quote->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @elseif($quote->status === 'expired')
                            <span class="badge badge-danger">Expired</span>
                        @else
                            <span class="badge badge-info">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('quotes.show', $quote) }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">View</a>
                            <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">PDF</a>
                            @if($quote->status === 'sent')
                                <form method="POST" action="{{ route('quotes.convert', $quote) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">Convert</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        No quotes found. <a href="{{ route('quotes.create') }}" style="color: var(--accent);">Create your first quote</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 1.5rem;">
        {{ $quotes->links() }}
    </div>
</div>
@endsection

