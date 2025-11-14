@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Edit Invoice</h1>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Back</a>
</div>

<form method="POST" action="{{ route('invoices.update', $invoice) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Invoice Details</h2>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-input" required>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $invoice->client_id === $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date" class="form-input" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-input" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" name="tax_rate" class="form-input" value="{{ old('tax_rate', $invoice->tax_rate) }}" step="0.01" min="0" max="100">
            </div>

            <div class="form-group">
                <label class="form-label">Currency</label>
                <select name="currency_code" class="form-input" id="currency_select" required>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency['code'] }}" data-symbol="{{ $currency['symbol'] }}" {{ $invoice->currency_code === $currency['code'] ? 'selected' : '' }}>
                            {{ $currency['label'] }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="currency_symbol" id="currency_symbol" value="{{ $invoice->currency_symbol }}">
            </div>

            <div class="form-group">
                <label class="form-label">Exchange Rate</label>
                <input type="number" name="currency_rate" class="form-input" value="{{ old('currency_rate', $invoice->currency_rate) }}" step="0.000001" min="0.000001">
                <small style="color: var(--text-secondary);">Relative to your base currency</small>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Discount Type</label>
            <select name="discount_type" class="form-input" id="discount_type">
                <option value="none" {{ $invoice->discount_type === 'none' ? 'selected' : '' }}>None</option>
                <option value="percentage" {{ $invoice->discount_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                <option value="fixed" {{ $invoice->discount_type === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
            </select>
        </div>
        
        <div class="form-group" id="discount_value_group" style="{{ $invoice->discount_type === 'none' ? 'display: none;' : '' }}">
            <label class="form-label">Discount Value</label>
            <input type="number" name="discount_value" class="form-input" value="{{ old('discount_value', $invoice->discount_value) }}" step="0.01" min="0">
        </div>
        
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-input" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Terms</label>
            <textarea name="terms" class="form-input" rows="3">{{ old('terms', $invoice->terms) }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">PDF Template</label>
            <select name="pdf_template" class="form-input">
                @foreach($pdfTemplates as $key => $label)
                    <option value="{{ $key }}" {{ $invoice->pdf_template === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Attachments</label>
            <input type="file" name="attachments[]" class="form-input" multiple>
            <small style="color: var(--text-secondary);">Upload additional files (PDF, images, up to 5MB each)</small>
        </div>

        @if($invoice->attachments->count())
            <div class="form-group">
                <label class="form-label">Existing Attachments</label>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($invoice->attachments as $attachment)
                        <li style="margin-bottom: 0.5rem;">
                            <a href="{{ route('invoices.attachments.download', [$invoice, $attachment]) }}" style="color: var(--accent); text-decoration: none;">
                                {{ $attachment->original_name }}
                            </a>
                            <small style="color: var(--text-secondary);">({{ number_format($attachment->file_size / 1024, 1) }} KB)</small>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Invoice Items</h2>
        
        <div id="items-container">
            @foreach($invoice->items as $index => $item)
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
        <button type="submit" class="btn btn-primary">Update Invoice</button>
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
let itemCount = {{ $invoice->items->count() }};

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

const currencySelect = document.getElementById('currency_select');
const currencySymbolInput = document.getElementById('currency_symbol');
if (currencySelect && currencySymbolInput) {
    const updateSymbol = () => {
        const option = currencySelect.options[currencySelect.selectedIndex];
        currencySymbolInput.value = option.getAttribute('data-symbol') || currencySymbolInput.value;
    };
    updateSymbol();
    currencySelect.addEventListener('change', updateSymbol);
}
</script>
@endsection

