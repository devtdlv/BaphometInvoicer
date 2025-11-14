<?php

namespace App\Console\Commands;

use App\Mail\InvoiceReminder;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders';
    protected $description = 'Send reminder emails for upcoming and overdue invoices.';

    public function handle(): int
    {
        $invoices = Invoice::with(['client', 'user'])
            ->whereIn('status', ['sent', 'overdue'])
            ->whereNull('paid_at')
            ->where(function ($query) {
                $query->whereBetween('due_date', [now()->subDays(7), now()->addDays(3)])
                      ->orWhere('status', 'overdue');
            })
            ->where(function ($query) {
                $query->whereNull('last_reminder_sent_at')
                      ->orWhere('last_reminder_sent_at', '<=', now()->subDay());
            })
            ->get();

        $count = 0;

        foreach ($invoices as $invoice) {
            if (!$invoice->client || !$invoice->client->email) {
                continue;
            }

            try {
                Mail::to($invoice->client->email)->send(new InvoiceReminder($invoice));
                $invoice->forceFill(['last_reminder_sent_at' => now()])->save();
                $count++;
            } catch (\Throwable $e) {
                Log::error('Failed to send invoice reminder', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Invoice reminders sent: {$count}");

        return Command::SUCCESS;
    }
}

