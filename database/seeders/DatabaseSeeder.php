<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_client' => false,
        ]);

        $this->command->info('✓ Created admin user: admin@example.com / password');

        // Create a test client user
        $clientUser = User::create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'is_client' => true,
        ]);

        $this->command->info('✓ Created client user: client@example.com / password');

        // Create a sample client
        $client = Client::create([
            'user_id' => $admin->id,
            'name' => 'Acme Corporation',
            'email' => 'billing@acme.com',
            'company' => 'Acme Corporation',
            'phone' => '+1 (555) 123-4567',
            'address_line_1' => '123 Business Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'United States',
            'tax_id' => 'TAX-123456',
        ]);

        $this->command->info('✓ Created sample client: Acme Corporation');

        // Create a sample invoice
        $invoice = Invoice::create([
            'user_id' => $admin->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-202411-0001',
            'status' => 'sent',
            'issue_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'tax_rate' => 10.00,
            'discount_type' => 'none',
            'notes' => 'Thank you for your business!',
            'terms' => 'Payment is due within 30 days. Late payments may incur a 5% fee.',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Web Development Services',
            'quantity' => 40,
            'price' => 150.00,
            'tax_rate' => 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'UI/UX Design',
            'quantity' => 20,
            'price' => 200.00,
            'tax_rate' => 0,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consultation Hours',
            'quantity' => 10,
            'price' => 100.00,
            'tax_rate' => 0,
        ]);

        $invoice->load('items');
        $invoice->calculateTotal();
        $invoice->save();

        $this->command->info('✓ Created sample invoice: INV-202411-0001');

        // Create a paid invoice
        $paidInvoice = Invoice::create([
            'user_id' => $admin->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-202410-0001',
            'status' => 'paid',
            'issue_date' => now()->subDays(35),
            'due_date' => now()->subDays(5),
            'paid_at' => now()->subDays(2),
            'payment_method' => 'stripe',
            'tax_rate' => 10.00,
            'discount_type' => 'percentage',
            'discount_value' => 5.00,
            'notes' => 'Payment received. Thank you!',
            'terms' => 'Payment is due within 30 days.',
        ]);

        InvoiceItem::create([
            'invoice_id' => $paidInvoice->id,
            'description' => 'Monthly Retainer',
            'quantity' => 1,
            'price' => 5000.00,
            'tax_rate' => 0,
        ]);

        $paidInvoice->load('items');
        $paidInvoice->calculateTotal();
        $paidInvoice->save();

        $this->command->info('✓ Created sample paid invoice: INV-202410-0001');

        // Create a sample quote
        $quote = Quote::create([
            'user_id' => $admin->id,
            'client_id' => $client->id,
            'quote_number' => 'QUO-202411-0001',
            'status' => 'sent',
            'issue_date' => now()->subDays(2),
            'expiry_date' => now()->addDays(28),
            'tax_rate' => 10.00,
            'discount_type' => 'fixed',
            'discount_value' => 500.00,
            'notes' => 'This quote is valid for 30 days.',
            'terms' => 'Acceptance of this quote constitutes agreement to the terms and conditions.',
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'E-commerce Platform Development',
            'quantity' => 1,
            'price' => 15000.00,
            'tax_rate' => 0,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Payment Gateway Integration',
            'quantity' => 1,
            'price' => 2500.00,
            'tax_rate' => 0,
        ]);

        QuoteItem::create([
            'quote_id' => $quote->id,
            'description' => 'Training & Documentation',
            'quantity' => 8,
            'price' => 200.00,
            'tax_rate' => 0,
        ]);

        $quote->load('items');
        $quote->calculateTotal();
        $quote->save();

        $this->command->info('✓ Created sample quote: QUO-202411-0001');

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  Database seeded successfully!');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('Test Accounts:');
        $this->command->line('  Admin:  admin@example.com / password');
        $this->command->line('  Client: client@example.com / password');
        $this->command->newLine();
        $this->command->info('Sample Data:');
        $this->command->line('  • 1 Client (Acme Corporation)');
        $this->command->line('  • 2 Invoices (1 sent, 1 paid)');
        $this->command->line('  • 1 Quote (sent)');
        $this->command->newLine();
    }
}

