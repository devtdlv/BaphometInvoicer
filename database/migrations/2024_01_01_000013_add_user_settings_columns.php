<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('default_currency_code', 3)->default('USD')->after('is_client');
            $table->string('default_pdf_template')->default('classic')->after('default_currency_code');
            $table->string('company_name')->nullable()->after('default_pdf_template');
            $table->text('company_address')->nullable()->after('company_name');
            $table->string('company_phone')->nullable()->after('company_address');
            $table->string('company_email')->nullable()->after('company_phone');
            $table->string('company_website')->nullable()->after('company_email');
            $table->string('company_tax_id')->nullable()->after('company_website');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'default_currency_code',
                'default_pdf_template',
                'company_name',
                'company_address',
                'company_phone',
                'company_email',
                'company_website',
                'company_tax_id',
            ]);
        });
    }
};

