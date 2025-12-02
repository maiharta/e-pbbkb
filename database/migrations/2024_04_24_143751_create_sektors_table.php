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
        Schema::create('sektors', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('kode');
            $table->string('nama');
            $table->decimal('persentase_tarif', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sektors');
    }
};
