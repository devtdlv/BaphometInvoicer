<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function createInvoice(array $data, int $userId): Invoice
    {
        $invoice = Invoice::create([
            'user_id' => $userId,
            'client_id' => $data['client_id'],
            'invoice_number' => $this->generateInvoiceNumber(),
            'status' => 'draft',
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
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
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'tax_rate' => $itemData['tax_rate'] ?? 0,
            ]);
        }

        $invoice->load('items');
        $invoice->calculateTotal();
        $invoice->save();

        return $invoice;
    }

    public function updateInvoice(Invoice $invoice, array $data): void
    {
        $invoice->update([
            'client_id' => $data['client_id'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'tax_rate' => $data['tax_rate'] ?? 0,
            'discount_type' => $data['discount_type'] ?? 'none',
            'discount_value' => $data['discount_value'] ?? null,
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
            'currency_code' => $data['currency_code'] ?? $invoice->currency_code,
            'currency_symbol' => $data['currency_symbol'] ?? $invoice->currency_symbol,
            'currency_rate' => $data['currency_rate'] ?? $invoice->currency_rate,
            'pdf_template' => $data['pdf_template'] ?? $invoice->pdf_template,
        ]);

        $invoice->items()->delete();

        foreach ($data['items'] as $itemData) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'tax_rate' => $itemData['tax_rate'] ?? 0,
            ]);
        }

        $invoice->load('items');
        $invoice->calculateTotal();
        $invoice->save();
    }

    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['client', 'items', 'user']);
        
        $view = $this->resolveTemplateView($invoice->pdf_template);
        $pdf = Pdf::loadView($view, compact('invoice'));
        
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    protected function generateInvoiceNumber(): string
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

    protected function resolveTemplateView(string $template): string
    {
        return match ($template) {
            'modern' => 'pdf.templates.invoice_modern',
            default => 'pdf.invoice',
        };
    }
}

