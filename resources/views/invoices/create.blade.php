@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Create Invoice</h1>
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Back</a>
</div>

<form method="POST" action="{{ route('invoices.store') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Invoice Details</h2>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-input" required>
                    <option value="">Select a client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date" class="form-input" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" class="form-input" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tax Rate (%)</label>
                <input type="number" name="tax_rate" class="form-input" value="{{ old('tax_rate', 0) }}" step="0.01" min="0" max="100">
            </div>

            <div class="form-group">
                <label class="form-label">Currency</label>
                <select name="currency_code" class="form-input" id="currency_select" required>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency['code'] }}" data-symbol="{{ $currency['symbol'] }}" {{ old('currency_code', $user->default_currency_code ?? 'USD') === $currency['code'] ? 'selected' : '' }}>
                            {{ $currency['label'] }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $currencies[0]['symbol'] ?? '$') }}">
                <small style="color: var(--text-secondary);">Change default in <a href="{{ route('settings.index') }}" style="color: var(--accent);">Settings</a></small>
            </div>

            <div class="form-group">
                <label class="form-label">Exchange Rate</label>
                <input type="number" name="currency_rate" class="form-input" value="{{ old('currency_rate', 1) }}" step="0.000001" min="0.000001">
                <small style="color: var(--text-secondary);">Relative to your base currency (default 1)</small>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Discount Type</label>
            <select name="discount_type" class="form-input" id="discount_type">
                <option value="none">None</option>
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed Amount</option>
            </select>
        </div>
        
        <div class="form-group" id="discount_value_group" style="display: none;">
            <label class="form-label">Discount Value</label>
            <input type="number" name="discount_value" class="form-input" value="{{ old('discount_value', 0) }}" step="0.01" min="0">
        </div>
        
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-input" rows="3">{{ old('notes') }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Terms</label>
            <textarea name="terms" class="form-input" rows="3">{{ old('terms') }}</textarea>
        </div>

        <div class="form-group">
            <label class="form-label">PDF Template</label>
            <select name="pdf_template" class="form-input">
                @foreach($pdfTemplates as $key => $label)
                    <option value="{{ $key }}" {{ old('pdf_template', $user->default_pdf_template ?? 'classic') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <small style="color: var(--text-secondary);">Change default in <a href="{{ route('settings.index') }}" style="color: var(--accent);">Settings</a></small>
        </div>

        <div class="form-group">
            <label class="form-label">Attachments</label>
            <input type="file" name="attachments[]" class="form-input" multiple>
            <small style="color: var(--text-secondary);">Upload reference files (PDF, images, up to 5MB each)</small>
        </div>
    </div>
    
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Invoice Items</h2>
        
        <div id="items-container">
            <div class="item-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Description</label>
                    <input type="text" name="items[0][description]" class="form-input" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="items[0][quantity]" class="form-input" value="1" step="0.01" min="0.01" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Price</label>
                    <input type="number" name="items[0][price]" class="form-input" step="0.01" min="0" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Tax Rate (%)</label>
                    <input type="number" name="items[0][tax_rate]" class="form-input" value="0" step="0.01" min="0">
                </div>
                <button type="button" class="btn btn-danger" onclick="removeItem(this)" style="padding: 0.625rem;">Remove</button>
            </div>
        </div>
        
        <button type="button" onclick="addItem()" class="btn btn-secondary" style="margin-top: 1rem;">Add Item</button>
    </div>
    
    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <button type="submit" class="btn btn-primary">Create Invoice</button>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
let itemCount = 1;

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
        currencySymbolInput.value = option.getAttribute('data-symbol') || '$';
    };
    updateSymbol();
    currencySelect.addEventListener('change', updateSymbol);
}
</script>
@endsection

