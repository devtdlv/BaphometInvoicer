@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Edit Client</h1>
    <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Back</a>
</div>

<form method="POST" action="{{ route('clients.update', $client) }}">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Client Information</h2>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $client->name) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $client->email) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Company</label>
                <input type="text" name="company" class="form-input" value="{{ old('company', $client->company) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', $client->phone) }}">
            </div>
        </div>
        
        <h3 style="margin-top: 2rem; margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Address</h3>
        
        <div class="form-group">
            <label class="form-label">Address Line 1</label>
            <input type="text" name="address_line_1" class="form-input" value="{{ old('address_line_1', $client->address_line_1) }}">
        </div>
        
        <div class="form-group">
            <label class="form-label">Address Line 2</label>
            <input type="text" name="address_line_2" class="form-input" value="{{ old('address_line_2', $client->address_line_2) }}">
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-input" value="{{ old('city', $client->city) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" class="form-input" value="{{ old('state', $client->state) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Postal Code</label>
                <input type="text" name="postal_code" class="form-input" value="{{ old('postal_code', $client->postal_code) }}">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" class="form-input" value="{{ old('country', $client->country) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Tax ID</label>
                <input type="text" name="tax_id" class="form-input" value="{{ old('tax_id', $client->tax_id) }}">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-input" rows="4">{{ old('notes', $client->notes) }}</textarea>
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Update Client</button>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection

