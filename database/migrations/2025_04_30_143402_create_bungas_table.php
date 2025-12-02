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
        Schema::create('bungas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaporan_id')->constrained()->cascadeOnDelete();
            $table->datetime('waktu_bunga');
            $table->integer('bunga_ke')->default(1);
            $table->decimal('persentase_bunga', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->string('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bungas');
    }
};
