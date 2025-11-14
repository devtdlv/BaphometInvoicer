<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'file_name',
        'original_name',
        'mime_type',
        'file_size',
        'disk',
        'path',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

