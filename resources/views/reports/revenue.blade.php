@extends('layouts.app')

@section('title', 'Revenue Report')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Revenue Report</h1>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
</div>

<form method="GET" action="{{ route('reports.revenue') }}" class="card" style="margin-bottom: 2rem;">
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

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Revenue</p>
        <p style="font-size: 2rem; font-weight: 700; color: var(--success);">${{ number_format($totalRevenue, 2) }}</p>
    </div>
    <div class="card">
        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Invoices</p>
        <p style="font-size: 2rem; font-weight: 700; color: var(--accent);">{{ $totalInvoices }}</p>
    </div>
</div>

<div class="card">
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem;">Monthly Revenue Breakdown</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Month</th>
                <th>Invoices</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenue as $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->month . '-01')->format('F Y') }}</td>
                    <td>{{ $item->count }}</td>
                    <td style="font-weight: 600; color: var(--success);">${{ number_format($item->revenue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        No revenue data for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

