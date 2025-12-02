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
        Schema::create('pelaporan_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaporan_id')->constrained('pelaporans');
            $table->foreignId('penjualan_id')->nullable()->constrained('penjualans');
            $table->text('deskripsi');
            $table->enum('status', ['info', 'warning', 'danger'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('step')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaporan_notes');
    }
};
