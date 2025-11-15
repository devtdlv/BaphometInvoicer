<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
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
        'paid_at',
        'payment_method',
        'payment_reference',
        'last_reminder_sent_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'currency_rate' => 'decimal:6',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(InvoiceAttachment::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'sent' && $this->due_date < now();
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
        
        // Calculate discount
        $discountAmount = 0;
        if ($this->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($this->discount_value / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discountAmount = $this->discount_value;
        }

        // Calculate tax on subtotal after discount
        $taxableAmount = $subtotal - $discountAmount;
        $taxAmount = $taxableAmount * ($this->tax_rate / 100);

        $this->subtotal = $subtotal;
        $this->discount_amount = $discountAmount;
        $this->tax_amount = $taxAmount;
        $this->total = $taxableAmount + $taxAmount;
    }
}

