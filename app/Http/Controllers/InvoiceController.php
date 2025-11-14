<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function index()
    {
        $invoices = Invoice::with('client')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::where('user_id', auth()->id())->get();
        return view('invoices.create', compact('clients'));
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
        ]);

        $invoice = $this->invoiceService->createInvoice($validated, auth()->id());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load(['client', 'items', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        $invoice->load('items');
        $clients = Client::where('user_id', auth()->id())->get();
        return view('invoices.edit', compact('invoice', 'clients'));
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
        ]);

        $this->invoiceService->updateInvoice($invoice, $validated);

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
        
        // TODO: Send email notification
        
        return redirect()->back()->with('success', 'Invoice sent successfully.');
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        return $this->invoiceService->generatePdf($invoice);
    }
}

