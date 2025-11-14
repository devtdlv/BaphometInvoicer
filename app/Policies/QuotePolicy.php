<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function view(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id || 
               ($user->is_client && $quote->client->email === $user->email);
    }

    public function update(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id && !$user->is_client;
    }

    public function delete(User $user, Quote $quote): bool
    {
        return $user->id === $quote->user_id && !$user->is_client;
    }
}

