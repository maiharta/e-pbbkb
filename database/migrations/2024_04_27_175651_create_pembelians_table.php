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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->ulid();
            $table->foreignId('pelaporan_id')->constrained('pelaporans');
            $table->foreignId('kabupaten_id')->constrained('kabupatens');
            $table->foreignId('jenis_bbm_id')->constrained('jenis_bbms');
            $table->string('kode_jenis_bbm')->nullable();
            $table->string('nama_jenis_bbm')->nullable();
            $table->string('is_subsidi')->nullable();
            $table->string('penjual')->nullable();
            $table->decimal('volume', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
