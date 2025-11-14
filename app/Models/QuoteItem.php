<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'description',
        'quantity',
        'price',
        'tax_rate',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'tax_rate' => 'decimal:2',
        ];
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }
}

