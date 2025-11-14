@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Invoices</h1>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">Create Invoice</a>
</div>

<!-- Search and Filters -->
<div class="card" style="margin-bottom: 1.5rem;">
    <form method="GET" action="{{ route('invoices.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-input" placeholder="Invoice #, client name..." value="{{ request('search') }}">
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Client</label>
            <select name="client_id" class="form-input">
                <option value="">All Clients</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Sort By</label>
            <select name="sort_by" class="form-input">
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                <option value="issue_date" {{ request('sort_by') === 'issue_date' ? 'selected' : '' }}>Issue Date</option>
                <option value="due_date" {{ request('sort_by') === 'due_date' ? 'selected' : '' }}>Due Date</option>
                <option value="total" {{ request('sort_by') === 'total' ? 'selected' : '' }}>Amount</option>
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1rem;">Filter</button>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary" style="padding: 0.625rem 1rem;">Clear</a>
        </div>
    </form>
</div>

<div class="card">
    <form id="bulk-form" method="POST" action="{{ route('invoices.bulk') }}" style="margin-bottom: 1rem; display: none;">
        @csrf
        <input type="hidden" name="action" id="bulk-action">
        <input type="hidden" name="invoice_ids" id="bulk-invoice-ids">
        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-tertiary); border-radius: 0.5rem;">
            <span id="bulk-count" style="font-weight: 600;"></span>
            <select id="bulk-action-select" class="form-input" style="width: auto;">
                <option value="">Select action...</option>
                <option value="mark_sent">Mark as Sent</option>
                <option value="mark_paid">Mark as Paid</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Apply</button>
            <button type="button" onclick="clearSelection()" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Cancel</button>
        </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 40px;"><input type="checkbox" id="select-all" onchange="toggleAll(this)"></th>
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
                    <td><input type="checkbox" class="invoice-checkbox" value="{{ $invoice->id }}" onchange="updateBulkActions()"></td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->client->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
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
                    <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
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

<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.invoice-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkActions();
}

function updateBulkActions() {
    const checked = document.querySelectorAll('.invoice-checkbox:checked');
    const bulkForm = document.getElementById('bulk-form');
    const bulkCount = document.getElementById('bulk-count');
    
    if (checked.length > 0) {
        bulkForm.style.display = 'block';
        bulkCount.textContent = `${checked.length} invoice(s) selected`;
        
        const ids = Array.from(checked).map(cb => cb.value);
        document.getElementById('bulk-invoice-ids').value = JSON.stringify(ids);
    } else {
        bulkForm.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.invoice-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    updateBulkActions();
}

document.getElementById('bulk-form').addEventListener('submit', function(e) {
    const action = document.getElementById('bulk-action-select').value;
    if (!action) {
        e.preventDefault();
        alert('Please select an action');
        return false;
    }
    document.getElementById('bulk-action').value = action;
});
</script>
@endsection

