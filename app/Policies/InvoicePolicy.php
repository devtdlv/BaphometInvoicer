<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id || 
               ($user->is_client && $invoice->client->email === $user->email);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id && !$user->is_client;
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id && !$user->is_client;
    }
}

