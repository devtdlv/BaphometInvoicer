<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function create(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $validated = $request->validate([
            'gateway' => 'required|in:stripe,paypal',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->total,
        ]);

        return $this->paymentService->processPayment($invoice, $validated);
    }

    public function callback(Request $request, string $gateway)
    {
        return $this->paymentService->handleCallback($request, $gateway);
    }

    public function success(Invoice $invoice)
    {
        return view('payments.success', compact('invoice'));
    }

    public function cancel(Invoice $invoice)
    {
        return view('payments.cancel', compact('invoice'));
    }
}

