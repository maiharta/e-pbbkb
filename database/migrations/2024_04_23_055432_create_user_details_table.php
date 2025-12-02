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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kabupaten_id')->constrained('kabupatens')->onDelete('cascade');
            $table->string('npwpd')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->string('alamat')->nullable();
            $table->string('filepath_berkas_persyaratan')->nullable();
            $table->boolean('is_user_readonly')->default(false);
            $table->text('catatan_revisi')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
