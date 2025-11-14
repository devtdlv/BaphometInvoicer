@extends('layouts.app')

@section('title', 'Edit Quote')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Edit Quote</h1>
    <a href="{{ route('quotes.show', $quote) }}" class="btn btn-secondary">Back</a>
</div>

<form method="POST" action="{{ route('quotes.update', $quote) }}">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Quote Details</h2>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-input" required>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $quote->client_id === $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date" class="form-input" value="{{ old('issue_date', $quote->issue_date->format('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-input" value="{{ old('expiry_date', $quote->expiry_date ? $quote->expiry_date->format('Y-m-d') : '') }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" name="tax_rate" class="form-input" value="{{ old('tax_rate', $quote->tax_rate) }}" step="0.01" min="0" max="100">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Discount Type</label>
            <select name="discount_type" class="form-input" id="discount_type">
                <option value="none" {{ $quote->discount_type === 'none' ? 'selected' : '' }}>None</option>
                <option value="percentage" {{ $quote->discount_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                <option value="fixed" {{ $quote->discount_type === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
            </select>
        </div>
        
        <div class="form-group" id="discount_value_group" style="{{ $quote->discount_type === 'none' ? 'display: none;' : '' }}">
            <label class="form-label">Discount Value</label>
            <input type="number" name="discount_value" class="form-input" value="{{ old('discount_value', $quote->discount_value) }}" step="0.01" min="0">
        </div>
        
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-input" rows="3">{{ old('notes', $quote->notes) }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Terms</label>
            <textarea name="terms" class="form-input" rows="3">{{ old('terms', $quote->terms) }}</textarea>
        </div>
    </div>
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Quote Items</h2>
        
        <div id="items-container">
            @foreach($quote->items as $index => $item)
                <div class="item-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Description</label>
                        <input type="text" name="items[{{ $index }}][description]" class="form-input" value="{{ $item->description }}" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[{{ $index }}][quantity]" class="form-input" value="{{ $item->quantity }}" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Price</label>
                        <input type="number" name="items[{{ $index }}][price]" class="form-input" value="{{ $item->price }}" step="0.01" min="0" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tax Rate (%)</label>
                        <input type="number" name="items[{{ $index }}][tax_rate]" class="form-input" value="{{ $item->tax_rate }}" step="0.01" min="0">
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeItem(this)" style="padding: 0.625rem;">Remove</button>
                </div>
            @endforeach
        </div>
        
        <button type="button" onclick="addItem()" class="btn btn-secondary" style="margin-top: 1rem;">Add Item</button>
    </div>
    
    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Update Quote</button>
        <a href="{{ route('quotes.show', $quote) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
let itemCount = {{ $quote->items->count() }};

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row';
    newItem.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;';
    newItem.innerHTML = `
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Description</label>
            <input type="text" name="items[${itemCount}][description]" class="form-input" required>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Quantity</label>
            <input type="number" name="items[${itemCount}][quantity]" class="form-input" value="1" step="0.01" min="0.01" required>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Price</label>
            <input type="number" name="items[${itemCount}][price]" class="form-input" step="0.01" min="0" required>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Tax Rate (%)</label>
            <input type="number" name="items[${itemCount}][tax_rate]" class="form-input" value="0" step="0.01" min="0">
        </div>
        <button type="button" class="btn btn-danger" onclick="removeItem(this)" style="padding: 0.625rem;">Remove</button>
    `;
    container.appendChild(newItem);
    itemCount++;
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
    }
}

document.getElementById('discount_type').addEventListener('change', function() {
    const valueGroup = document.getElementById('discount_value_group');
    if (this.value !== 'none') {
        valueGroup.style.display = 'block';
    } else {
        valueGroup.style.display = 'none';
    }
});
</script>
@endsection

