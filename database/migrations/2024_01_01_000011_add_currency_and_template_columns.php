<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('total');
            $table->string('currency_symbol', 5)->default('$')->after('currency_code');
            $table->decimal('currency_rate', 15, 6)->default(1)->after('currency_symbol');
            $table->string('pdf_template')->default('classic')->after('terms');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('paid_at');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('total');
            $table->string('currency_symbol', 5)->default('$')->after('currency_code');
            $table->decimal('currency_rate', 15, 6)->default(1)->after('currency_symbol');
            $table->string('pdf_template')->default('classic')->after('terms');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'currency_code',
                'currency_symbol',
                'currency_rate',
                'pdf_template',
                'last_reminder_sent_at',
            ]);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'currency_code',
                'currency_symbol',
                'currency_rate',
                'pdf_template',
            ]);
        });
    }
};

