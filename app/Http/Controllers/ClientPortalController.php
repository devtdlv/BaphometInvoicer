<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class ClientPortalController extends Controller
{
    public function dashboard()
    {
        $invoices = Invoice::whereHas('client', function ($query) {
            $query->where('email', auth()->user()->email);
        })
        ->with('client')
        ->latest()
        ->paginate(20);

        return view('client-portal.dashboard', compact('invoices'));
    }

    public function invoice(Invoice $invoice)
    {
        // Verify client has access to this invoice
        $hasAccess = Invoice::where('id', $invoice->id)
            ->whereHas('client', function ($query) {
                $query->where('email', auth()->user()->email);
            })
            ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $invoice->load(['client', 'items', 'payments']);
        return view('client-portal.invoice', compact('invoice'));
    }
}

