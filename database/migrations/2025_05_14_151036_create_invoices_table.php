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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->ulid();
            $table->foreignId('pelaporan_id')->constrained();
            $table->string('invoice_number')->unique();
            $table->integer('receipt_number')->unique();
            // User Detail
            $table->string('customer_npwpd')->nullable(); // NPWPD number
            $table->string('customer_name')->nullable(); // Name of the customer
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->integer('month')->nullable(); // Month of the invoice
            $table->integer('year')->nullable(); // Year of the invoice
            $table->text('description')->nullable(); // Description of the invoice
            // Invoice Detail
            $table->decimal('amount', 10, 2);
            $table->json('items'); // JSON array of items
            $table->string('payment_status')->default('pending'); // pending, paid, expired
            // Sipay
            $table->string('sipay_record_id')->nullable(); // ID from payment gateway
            $table->string('sipay_virtual_account')->nullable(); // Virtual account number
            $table->timestamp('sipay_transaction_date')->nullable(); // Date of transaction
            $table->timestamp('sipay_expired_date')->nullable(); // Expiration date for the transaction
            $table->string('sipay_nomor_tagihan')->nullable(); // Tagihan number
            $table->boolean('sipay_status_invoice')->default(false); // true if invoice is paid
            $table->string('sipay_status_bpd')->nullable(); // e.g., credit_card, bank_transfer
            // expired
            $table->timestamp('expires_at')->nullable(); // Expiration date for the invoice
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
