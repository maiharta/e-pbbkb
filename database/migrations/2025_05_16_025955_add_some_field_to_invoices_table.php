<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('sipay_payment_date_paid')->nullable()->after('sipay_status_bpd');
            $table->string('sipay_payment_date_kasda')->nullable()->after('sipay_payment_date_paid');
            $table->string('sipay_invoice')->nullable()->after('sipay_payment_date_kasda');
            $table->json('sipay_response')->nullable()->after('sipay_invoice');

            $table->dropColumn('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('sipay_payment_date_paid');
            $table->dropColumn('sipay_payment_date_kasda');
            $table->dropColumn('sipay_invoice');
            $table->dropColumn('sipay_response');

            $table->integer('receipt_number')->unique()->after('invoice_number'); // Re-add receipt_number
        });
    }
};
