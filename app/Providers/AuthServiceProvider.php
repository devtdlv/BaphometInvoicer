<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Policies\ClientPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\QuotePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
        Quote::class => QuotePolicy::class,
        Client::class => ClientPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}

