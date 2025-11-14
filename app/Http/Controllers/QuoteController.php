<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quote;
use App\Services\QuoteService;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteService $quoteService
    ) {}

    public function index()
    {
        $quotes = Quote::with('client')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $clients = Client::where('user_id', auth()->id())->get();
        $currencies = $this->currencyOptions();
        $pdfTemplates = $this->pdfTemplates();

        return view('quotes.create', compact('clients', 'currencies', 'pdfTemplates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
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
        ]);

        $quote = $this->quoteService->createQuote($validated, auth()->id());

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote created successfully.');
    }

    public function show(Quote $quote)
    {
        $this->authorize('view', $quote);
        
        $quote->load(['client', 'items']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $this->authorize('update', $quote);
        
        $quote->load('items');
        $clients = Client::where('user_id', auth()->id())->get();
        $currencies = $this->currencyOptions();
        $pdfTemplates = $this->pdfTemplates();

        return view('quotes.edit', compact('quote', 'clients', 'currencies', 'pdfTemplates'));
    }

    public function update(Request $request, Quote $quote)
    {
        $this->authorize('update', $quote);

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
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
        ]);

        $this->quoteService->updateQuote($quote, $validated);

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $this->authorize('delete', $quote);
        
        $quote->delete();
        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

    public function send(Quote $quote)
    {
        $this->authorize('update', $quote);
        
        $quote->update(['status' => 'sent']);
        
        // TODO: Send email notification
        
        return redirect()->back()->with('success', 'Quote sent successfully.');
    }

    public function convert(Quote $quote)
    {
        $this->authorize('update', $quote);
        
        $invoice = $this->quoteService->convertToInvoice($quote);
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Quote converted to invoice successfully.');
    }

    public function pdf(Quote $quote)
    {
        $this->authorize('view', $quote);
        
        return $this->quoteService->generatePdf($quote);
    }
}

