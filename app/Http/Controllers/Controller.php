<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function currencyOptions(): array
    {
        return [
            ['code' => 'USD', 'symbol' => '$', 'label' => 'USD — US Dollar'],
            ['code' => 'EUR', 'symbol' => '€', 'label' => 'EUR — Euro'],
            ['code' => 'GBP', 'symbol' => '£', 'label' => 'GBP — British Pound'],
            ['code' => 'AUD', 'symbol' => 'A$', 'label' => 'AUD — Australian Dollar'],
            ['code' => 'CAD', 'symbol' => 'C$', 'label' => 'CAD — Canadian Dollar'],
            ['code' => 'JPY', 'symbol' => '¥', 'label' => 'JPY — Japanese Yen'],
            ['code' => 'NZD', 'symbol' => 'NZ$', 'label' => 'NZD — New Zealand Dollar'],
            ['code' => 'SGD', 'symbol' => 'S$', 'label' => 'SGD — Singapore Dollar'],
        ];
    }

    protected function pdfTemplates(): array
    {
        return [
            'classic' => 'Classic',
            'modern' => 'Modern Minimal',
        ];
    }
}

