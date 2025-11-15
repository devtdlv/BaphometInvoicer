@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Settings</h1>
</div>

<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    @method('PUT')
    
    <!-- Default Preferences -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Default Preferences</h2>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Default Currency</label>
                <select name="default_currency_code" class="form-input" required>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency['code'] }}" {{ $user->default_currency_code === $currency['code'] ? 'selected' : '' }}>
                            {{ $currency['label'] }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-secondary);">This will be used as the default for new invoices and quotes</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Default PDF Template</label>
                <select name="default_pdf_template" class="form-input" required>
                    @foreach($templates as $key => $label)
                        <option value="{{ $key }}" {{ $user->default_pdf_template === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-secondary);">Default template for PDF generation</small>
            </div>
        </div>
    </div>
    
    <!-- Company Information -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Company Information</h2>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.875rem;">
            This information will be used in your invoices and quotes as the "From" details.
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-input" value="{{ old('company_name', $user->company_name) }}" placeholder="Your Company Name">
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="company_email" class="form-input" value="{{ old('company_email', $user->company_email) }}" placeholder="billing@company.com">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="company_phone" class="form-input" value="{{ old('company_phone', $user->company_phone) }}" placeholder="+1 (555) 123-4567">
            </div>
            
            <div class="form-group">
                <label class="form-label">Website</label>
                <input type="url" name="company_website" class="form-input" value="{{ old('company_website', $user->company_website) }}" placeholder="https://company.com">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label">Address</label>
            <textarea name="company_address" class="form-input" rows="3" placeholder="123 Business St, City, State, ZIP">{{ old('company_address', $user->company_address) }}</textarea>
        </div>
        
        <div class="form-group">
            <label class="form-label">Tax ID / VAT Number</label>
            <input type="text" name="company_tax_id" class="form-input" value="{{ old('company_tax_id', $user->company_tax_id) }}" placeholder="TAX-123456 or VAT123456">
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem;">
        <button type="submit" class="btn btn-primary">Save Settings</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endsection

