<?php

namespace App\Console;

use App\Console\Commands\SendInvoiceReminders;
use App\Console\Commands\UpdateOverdueInvoices;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        UpdateOverdueInvoices::class,
        SendInvoiceReminders::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Update overdue invoices daily
        $schedule->command('invoices:update-overdue')->dailyAt('07:00');

        // Send reminder emails every morning
        $schedule->command('invoices:send-reminders')->dailyAt('08:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

