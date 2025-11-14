@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Dashboard</h1>
    <p style="color: var(--text-secondary); margin-top: 0.5rem;">Welcome back, {{ auth()->user()->name }}!</p>
</div>

<!-- Revenue Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--success);">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Total Revenue</p>
                <p style="font-size: 1.75rem; font-weight: 700; color: var(--success);">${{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div style="font-size: 2rem;">💰</div>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-secondary);">All time paid invoices</p>
    </div>

    <div class="card" style="border-left: 4px solid var(--accent);">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">This Month</p>
                <p style="font-size: 1.75rem; font-weight: 700; color: var(--accent);">${{ number_format($monthlyRevenue, 2) }}</p>
            </div>
            <div style="font-size: 2rem;">📈</div>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-secondary);">Revenue this month</p>
    </div>

    <div class="card" style="border-left: 4px solid var(--warning);">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Pending</p>
                <p style="font-size: 1.75rem; font-weight: 700; color: var(--warning);">${{ number_format($pendingAmount, 2) }}</p>
            </div>
            <div style="font-size: 2rem;">⏳</div>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-secondary);">Awaiting payment</p>
    </div>

    <div class="card" style="border-left: 4px solid var(--danger);">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
            <div>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Overdue</p>
                <p style="font-size: 1.75rem; font-weight: 700; color: var(--danger);">${{ number_format($overdueAmount, 2) }}</p>
            </div>
            <div style="font-size: 2rem;">⚠️</div>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-secondary);">Past due invoices</p>
    </div>
</div>

<!-- Quick Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="text-align: center;">
        <p style="font-size: 2rem; font-weight: 700; color: var(--accent); margin-bottom: 0.5rem;">{{ $totalInvoices }}</p>
        <p style="font-size: 0.875rem; color: var(--text-secondary);">Total Invoices</p>
        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 0.75rem; font-size: 0.75rem;">
            <span class="badge badge-success">{{ $paidInvoices }} Paid</span>
            <span class="badge badge-warning">{{ $sentInvoices }} Sent</span>
            <span class="badge badge-info">{{ $draftInvoices }} Draft</span>
        </div>
    </div>

    <div class="card" style="text-align: center;">
        <p style="font-size: 2rem; font-weight: 700; color: var(--accent); margin-bottom: 0.5rem;">{{ $totalQuotes }}</p>
        <p style="font-size: 0.875rem; color: var(--text-secondary);">Total Quotes</p>
        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 0.75rem; font-size: 0.75rem;">
            <span class="badge badge-success">{{ $acceptedQuotes }} Accepted</span>
            <span class="badge badge-warning">{{ $pendingQuotes }} Pending</span>
        </div>
    </div>

    <div class="card" style="text-align: center;">
        <p style="font-size: 2rem; font-weight: 700; color: var(--accent); margin-bottom: 0.5rem;">{{ $totalClients }}</p>
        <p style="font-size: 0.875rem; color: var(--text-secondary);">Total Clients</p>
    </div>
</div>

<!-- Main Content Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Recent Invoices -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600;">Recent Invoices</h2>
            <a href="{{ route('invoices.index') }}" style="color: var(--accent); text-decoration: none; font-size: 0.875rem;">View All →</a>
        </div>
        @forelse($recentInvoices as $invoice)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem; margin-bottom: 0.75rem;">
                <div>
                    <p style="font-weight: 600; margin-bottom: 0.25rem;">
                        <a href="{{ route('invoices.show', $invoice) }}" style="color: var(--text-primary); text-decoration: none;">{{ $invoice->invoice_number }}</a>
                    </p>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">{{ $invoice->client->name }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-weight: 600; color: var(--accent); margin-bottom: 0.25rem;">${{ number_format($invoice->total, 2) }}</p>
                    @if($invoice->status === 'paid')
                        <span class="badge badge-success">Paid</span>
                    @elseif($invoice->status === 'sent')
                        <span class="badge badge-warning">Sent</span>
                    @else
                        <span class="badge badge-info">Draft</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No invoices yet</p>
        @endforelse
    </div>

    <!-- Overdue Invoices -->
    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">Overdue Invoices</h2>
        @forelse($overdueInvoices as $invoice)
            <div style="padding: 1rem; background: rgba(239, 68, 68, 0.1); border-left: 3px solid var(--danger); border-radius: 0.5rem; margin-bottom: 0.75rem;">
                <p style="font-weight: 600; margin-bottom: 0.25rem;">
                    <a href="{{ route('invoices.show', $invoice) }}" style="color: var(--text-primary); text-decoration: none;">{{ $invoice->invoice_number }}</a>
                </p>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">{{ $invoice->client->name }}</p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p style="font-weight: 600; color: var(--danger);">${{ number_format($invoice->total, 2) }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-secondary);">
                        {{ \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No overdue invoices 🎉</p>
        @endforelse
    </div>
</div>

<!-- Bottom Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <!-- Top Clients -->
    <div class="card">
        <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">Top Clients</h2>
        @forelse($topClients as $client)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem; margin-bottom: 0.75rem;">
                <div>
                    <p style="font-weight: 600; margin-bottom: 0.25rem;">
                        <a href="{{ route('clients.show', $client) }}" style="color: var(--text-primary); text-decoration: none;">{{ $client->name }}</a>
                    </p>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">{{ $client->email }}</p>
                </div>
                <p style="font-weight: 600; color: var(--success);">${{ number_format($client->total_revenue ?? 0, 2) }}</p>
            </div>
        @empty
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No clients yet</p>
        @endforelse
    </div>

    <!-- Recent Quotes -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600;">Recent Quotes</h2>
            <a href="{{ route('quotes.index') }}" style="color: var(--accent); text-decoration: none; font-size: 0.875rem;">View All →</a>
        </div>
        @forelse($recentQuotes as $quote)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem; margin-bottom: 0.75rem;">
                <div>
                    <p style="font-weight: 600; margin-bottom: 0.25rem;">
                        <a href="{{ route('quotes.show', $quote) }}" style="color: var(--text-primary); text-decoration: none;">{{ $quote->quote_number }}</a>
                    </p>
                    <p style="font-size: 0.875rem; color: var(--text-secondary);">{{ $quote->client->name }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-weight: 600; color: var(--accent); margin-bottom: 0.25rem;">${{ number_format($quote->total, 2) }}</p>
                    @if($quote->status === 'accepted')
                        <span class="badge badge-success">Accepted</span>
                    @elseif($quote->status === 'sent')
                        <span class="badge badge-warning">Sent</span>
                    @else
                        <span class="badge badge-info">Draft</span>
                    @endif
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">No quotes yet</p>
        @endforelse
    </div>
</div>
@endsection

