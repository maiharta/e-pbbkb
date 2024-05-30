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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->ulid();
            $table->foreignId('pelaporan_id')->constrained('pelaporans');
            $table->foreignId('kabupaten_id')->constrained('kabupatens');
            $table->foreignId('jenis_bbm_id')->constrained('jenis_bbms');
            $table->foreignId('sektor_id')->constrained('sektors');
            $table->string('kode_jenis_bbm')->nullable();
            $table->string('nama_jenis_bbm')->nullable();
            $table->string('is_subsidi')->nullable();
            $table->decimal('persentase_tarif_jenis_bbm', 5, 2)->default(0);
            $table->string('kode_sektor')->nullable();
            $table->string('nama_sektor')->nullable();
            $table->decimal('persentase_tarif_sektor', 5, 2)->default(0);
            $table->string('pembeli')->nullable();
            $table->decimal('volume', 15, 2);
            $table->decimal('dpp', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
