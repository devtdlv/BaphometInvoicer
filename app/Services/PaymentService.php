<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\ApiErrorException;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function processPayment(Invoice $invoice, array $data)
    {
        return match($data['gateway']) {
            'stripe' => $this->processStripePayment($invoice, $data['amount']),
            'paypal' => $this->processPayPalPayment($invoice, $data['amount']),
            default => redirect()->back()->with('error', 'Invalid payment gateway.'),
        };
    }

    protected function processStripePayment(Invoice $invoice, float $amount)
    {
        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "Invoice {$invoice->invoice_number}",
                        ],
                        'unit_amount' => (int)($amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payments.success', $invoice) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.cancel', $invoice),
                'metadata' => [
                    'invoice_id' => $invoice->id,
                ],
            ]);

            return redirect($session->url);
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    protected function processPayPalPayment(Invoice $invoice, float $amount)
    {
        // PayPal integration would go here
        // For now, return a placeholder
        return redirect()->back()->with('info', 'PayPal integration coming soon.');
    }

    public function handleCallback(Request $request, string $gateway)
    {
        return match($gateway) {
            'stripe' => $this->handleStripeCallback($request),
            'paypal' => $this->handlePayPalCallback($request),
            default => redirect()->route('invoices.index')->with('error', 'Invalid payment gateway.'),
        };
    }

    protected function handleStripeCallback(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('invoices.index')->with('error', 'Invalid payment session.');
        }

        try {
            $session = StripeSession::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                $invoice = Invoice::findOrFail($session->metadata->invoice_id);
                
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $session->amount_total / 100,
                    'payment_method' => 'card',
                    'payment_reference' => $session->id,
                    'paid_at' => now(),
                    'status' => 'completed',
                    'gateway' => 'stripe',
                    'gateway_transaction_id' => $session->payment_intent,
                ]);

                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'payment_method' => 'stripe',
                    'payment_reference' => $session->id,
                ]);

                return redirect()->route('payments.success', $invoice)
                    ->with('success', 'Payment processed successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Stripe callback error: ' . $e->getMessage());
        }

        return redirect()->route('invoices.index')->with('error', 'Payment verification failed.');
    }

    protected function handlePayPalCallback(Request $request)
    {
        // PayPal callback handling would go here
        return redirect()->route('invoices.index')->with('info', 'PayPal callback handling coming soon.');
    }
}

