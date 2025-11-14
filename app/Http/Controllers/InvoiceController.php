<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSent;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index(Request $request)
    {
        $query = Invoice::with('client')
            ->where('user_id', auth()->id());

        // Search by invoice number or client name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $invoices = $query->paginate(20)->withQueryString();
        $clients = Client::where('user_id', auth()->id())->get();

        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create()
    {
        $clients = Client::where('user_id', auth()->id())->get();
        $currencies = $this->currencyOptions();
        $pdfTemplates = $this->pdfTemplates();

        return view('invoices.create', compact('clients', 'currencies', 'pdfTemplates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'currency_rate' => 'nullable|numeric|min:0.000001',
            'pdf_template' => 'required|in:' . implode(',', array_keys($this->pdfTemplates())),
            'attachments.*' => 'file|max:5120',
        ]);

        $invoice = $this->invoiceService->createInvoice($validated, auth()->id());

        if ($request->hasFile('attachments')) {
            $this->storeAttachments($invoice, $request->file('attachments'));
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['client', 'items', 'payments', 'attachments']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        $invoice->load(['items', 'attachments']);
        $clients = Client::where('user_id', auth()->id())->get();
        $currencies = $this->currencyOptions();
        $pdfTemplates = $this->pdfTemplates();

        return view('invoices.edit', compact('invoice', 'clients', 'currencies', 'pdfTemplates'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:5',
            'currency_rate' => 'nullable|numeric|min:0.000001',
            'pdf_template' => 'required|in:' . implode(',', array_keys($this->pdfTemplates())),
            'attachments.*' => 'file|max:5120',
        ]);

        $this->invoiceService->updateInvoice($invoice, $validated);

        if ($request->hasFile('attachments')) {
            $this->storeAttachments($invoice, $request->file('attachments'));
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        
        $invoice->delete();
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function send(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        $invoice->update(['status' => 'sent']);
        
        // Send email notification
        try {
            Mail::to($invoice->client->email)->send(new InvoiceSent($invoice));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send invoice email: ' . $e->getMessage());
        }
        
        return redirect()->back()->with('success', 'Invoice sent successfully.');
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        return $this->invoiceService->generatePdf($invoice);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_sent,mark_paid',
            'invoice_ids' => 'required|string',
        ]);

        $invoiceIds = json_decode($request->invoice_ids, true);
        
        if (!is_array($invoiceIds)) {
            return redirect()->back()->with('error', 'Invalid invoice selection.');
        }

        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->where('user_id', auth()->id())
            ->get();

        $count = 0;
        foreach ($invoices as $invoice) {
            $this->authorize('update', $invoice);
            
            match($request->action) {
                'delete' => $invoice->delete(),
                'mark_sent' => $invoice->update(['status' => 'sent']),
                'mark_paid' => $invoice->update(['status' => 'paid', 'paid_at' => now()]),
            };
            $count++;
        }

        $message = match($request->action) {
            'delete' => "{$count} invoice(s) deleted successfully.",
            'mark_sent' => "{$count} invoice(s) marked as sent.",
            'mark_paid' => "{$count} invoice(s) marked as paid.",
        };

        return redirect()->back()->with('success', $message);
    }

    public function downloadAttachment(Invoice $invoice, InvoiceAttachment $attachment)
    {
        $this->authorize('view', $invoice);
        abort_unless($attachment->invoice_id === $invoice->id, 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name);
    }

    protected function storeAttachments(Invoice $invoice, array $files): void
    {
        foreach ($files as $file) {
            if (!$file instanceof \Illuminate\Http\UploadedFile) {
                continue;
            }

            $path = $file->store("invoice-attachments/{$invoice->id}", 'public');

            InvoiceAttachment::create([
                'invoice_id' => $invoice->id,
                'file_name' => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'disk' => 'public',
                'path' => $path,
            ]);
        }
    }
}

