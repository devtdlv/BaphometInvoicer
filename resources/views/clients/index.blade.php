@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Clients</h1>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">Add Client</a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->company ?? 'N/A' }}</td>
                    <td>{{ $client->phone ?? 'N/A' }}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">View</a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem;">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        No clients found. <a href="{{ route('clients.create') }}" style="color: var(--accent);">Add your first client</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 1.5rem;">
        {{ $clients->links() }}
    </div>
</div>
@endsection

