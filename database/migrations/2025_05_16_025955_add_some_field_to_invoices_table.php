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
        });
    }
};
