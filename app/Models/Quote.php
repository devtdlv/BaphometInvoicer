<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'quote_number',
        'status',
        'issue_date',
        'expiry_date',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'currency_code',
        'currency_symbol',
        'currency_rate',
        'notes',
        'terms',
        'pdf_template',
        'converted_to_invoice_id',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'currency_rate' => 'decimal:6',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'converted_to_invoice_id');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    public function getCurrencySymbolAttribute(): string
    {
        // If currency_symbol column exists and has a value, use it
        if (isset($this->attributes['currency_symbol']) && $this->attributes['currency_symbol']) {
            return $this->attributes['currency_symbol'];
        }
        
        // Otherwise derive from currency_code
        return $this->getCurrencySymbol($this->currency_code ?? 'USD');
    }

    protected function getCurrencySymbol(string $code): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            'INR' => '₹',
            'NZD' => 'NZ$',
        ];

        return $symbols[strtoupper($code)] ?? '$';
    }

    public function calculateTotal(): void
    {
        $subtotal = $this->items->sum(fn($item) => $item->quantity * $item->price);
        
        $discountAmount = 0;
        if ($this->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($this->discount_value / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discountAmount = $this->discount_value;
        }

        $taxableAmount = $subtotal - $discountAmount;
        $taxAmount = $taxableAmount * ($this->tax_rate / 100);

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total = $taxableAmount + $taxAmount;
    }
}

