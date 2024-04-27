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
        Schema::create('jenis_bbms', function (Blueprint $table) {
            $table->id();
            $table->string('ulid')->unique();
            $table->string('kode');
            $table->string('nama');
            $table->boolean('is_subsidi')->default(true);
            $table->decimal('persentase_tarif', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_bbms');
    }
};
