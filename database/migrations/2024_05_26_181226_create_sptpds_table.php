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
        Schema::create('sptpds', function (Blueprint $table) {
            $table->id();
            $table->ulid();
            $table->foreignId('pelaporan_id')->constrained('pelaporans');
            $table->string('nomor')->nullable();
            $table->date('tanggal')->nullable();
            $table->date('wajib_pajak')->nullable();
            $table->date('jabatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sptpds');
    }
};
