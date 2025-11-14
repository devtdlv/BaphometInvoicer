<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\QuoteItem;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteService
{
    public function createQuote(array $data, int $userId): Quote
    {
        $quote = Quote::create([
            'user_id' => $userId,
            'client_id' => $data['client_id'],
            'quote_number' => $this->generateQuoteNumber(),
            'status' => 'draft',
            'issue_date' => $data['issue_date'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'tax_rate' => $data['tax_rate'] ?? 0,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => $data['discount_value'] ?? null,
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
            'currency_code' => $data['currency_code'] ?? 'USD',
            'currency_symbol' => $data['currency_symbol'] ?? '$',
            'currency_rate' => $data['currency_rate'] ?? 1,
            'pdf_template' => $data['pdf_template'] ?? 'classic',
        ]);

        foreach ($data['items'] as $itemData) {
            QuoteItem::create([
                'quote_id' => $quote->id,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'tax_rate' => $itemData['tax_rate'] ?? 0,
            ]);
        }

        $quote->load('items');
        $quote->calculateTotal();
        $quote->save();

        return $quote;
    }

    public function updateQuote(Quote $quote, array $data): void
    {
        $quote->update([
            'client_id' => $data['client_id'],
            'issue_date' => $data['issue_date'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'tax_rate' => $data['tax_rate'] ?? 0,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => $data['discount_value'] ?? null,
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
            'currency_code' => $data['currency_code'] ?? $quote->currency_code,
            'currency_symbol' => $data['currency_symbol'] ?? $quote->currency_symbol,
            'currency_rate' => $data['currency_rate'] ?? $quote->currency_rate,
            'pdf_template' => $data['pdf_template'] ?? $quote->pdf_template,
        ]);

        $quote->items()->delete();

        foreach ($data['items'] as $itemData) {
            QuoteItem::create([
                'quote_id' => $quote->id,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'tax_rate' => $itemData['tax_rate'] ?? 0,
            ]);
        }

        $quote->load('items');
        $quote->calculateTotal();
        $quote->save();
    }

    public function convertToInvoice(Quote $quote): Invoice
    {
        $invoice = Invoice::create([
            'user_id' => $quote->user_id,
            'client_id' => $quote->client_id,
            'invoice_number' => $this->generateInvoiceNumberForQuote(),
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(config('app.invoice_due_days', 30)),
            'tax_rate' => $quote->tax_rate,
            'discount_type' => $quote->discount_type,
            'discount_value' => $quote->discount_value,
            'notes' => $quote->notes,
            'terms' => $quote->terms,
            'currency_code' => $quote->currency_code,
            'currency_symbol' => $quote->currency_symbol,
            'currency_rate' => $quote->currency_rate,
            'pdf_template' => $quote->pdf_template,
        ]);

        foreach ($quote->items as $quoteItem) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $quoteItem->description,
                'quantity' => $quoteItem->quantity,
                'price' => $quoteItem->price,
                'tax_rate' => $quoteItem->tax_rate,
            ]);
        }

        $invoice->load('items');
        $invoice->calculateTotal();
        $invoice->save();

        $quote->update([
            'status' => 'accepted',
            'converted_to_invoice_id' => $invoice->id,
        ]);

        return $invoice;
    }

    public function generatePdf(Quote $quote)
    {
        $quote->load(['client', 'items', 'user']);
        
        $pdf = Pdf::loadView($this->resolveTemplate($quote->pdf_template), compact('quote'));
        
        return $pdf->download("quote-{$quote->quote_number}.pdf");
    }

    protected function generateQuoteNumber(): string
    {
        $prefix = config('app.quote_prefix', 'QUO-');
        $year = now()->year;
        $month = now()->format('m');
        
        $lastQuote = Quote::where('quote_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('quote_number', 'desc')
            ->first();
        
        if ($lastQuote) {
            $lastNumber = (int) substr($lastQuote->quote_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('%s%s%s-%04d', $prefix, $year, $month, $nextNumber);
    }

    protected function generateInvoiceNumberForQuote(): string
    {
        $prefix = config('app.invoice_prefix', 'INV-');
        $year = now()->year;
        $month = now()->format('m');
        
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}{$year}{$month}%")
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return sprintf('%s%s%s-%04d', $prefix, $year, $month, $nextNumber);
    }

    protected function resolveTemplate(string $template): string
    {
        return match ($template) {
            'modern' => 'pdf.templates.quote_modern',
            default => 'pdf.quote',
        };
    }
}

