@extends('layouts.app')

@section('title', 'Client Report')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Client Report</h1>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
</div>

<form method="GET" action="{{ route('reports.client') }}" class="card" style="margin-bottom: 2rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-input" value="{{ $startDate }}">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-input" value="{{ $endDate }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<div class="card">
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">Top Clients by Revenue</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Email</th>
                <th>Invoices</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>
                        <a href="{{ route('clients.show', $client) }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">
                            {{ $client->name }}
                        </a>
                    </td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->invoice_count }}</td>
                    <td style="font-weight: 600; color: var(--success);">${{ number_format($client->total_revenue ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        No client data for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

