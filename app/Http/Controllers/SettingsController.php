<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currencies = $this->currencyOptions();
        $templates = $this->pdfTemplates();
        
        return view('settings.index', compact('user', 'currencies', 'templates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_currency_code' => 'required|string|size:3',
            'default_pdf_template' => 'required|string|in:classic,modern',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_tax_id' => 'nullable|string|max:100',
        ]);

        Auth::user()->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}

