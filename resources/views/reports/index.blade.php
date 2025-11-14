@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Reports & Analytics</h1>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <a href="{{ route('reports.revenue') }}" class="card" style="text-decoration: none; display: block; transition: transform 0.2s;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 3rem;">📊</div>
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Revenue Report</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">View revenue trends and statistics</p>
            </div>
        </div>
    </a>

    <a href="{{ route('reports.client') }}" class="card" style="text-decoration: none; display: block; transition: transform 0.2s;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 3rem;">👥</div>
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Client Report</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Top clients by revenue</p>
            </div>
        </div>
    </a>

    <a href="{{ route('reports.export', ['type' => 'invoices', 'format' => 'csv']) }}" class="card" style="text-decoration: none; display: block; transition: transform 0.2s;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 3rem;">📥</div>
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">Export Data</h2>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">Export invoices to CSV</p>
            </div>
        </div>
    </a>
</div>
@endsection

