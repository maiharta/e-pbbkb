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
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropForeign(['kabupaten_id']);
            $table->dropColumn('kabupaten_id');

            $table->decimal('persentase_tarif_jenis_bbm', 5, 2)->default(0);
            $table->text('alamat')->nullable();
            $table->string('nomor_kuitansi')->nullable();
            $table->date('tanggal')->nullable();
            $table->decimal('sisa_volume', 15,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreignId('kabupaten_id')->constrained('kabupatens');
            $table->dropColumn('persentase_tarif_jenis_bbm');
            $table->dropColumn('alamat');
            $table->dropColumn('nomor_kwitansi');
            $table->dropColumn('tanggal');
            $table->dropColumn('sisa_volume');
        });
    }
};
