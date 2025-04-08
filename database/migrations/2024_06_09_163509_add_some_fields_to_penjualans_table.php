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
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['kabupaten_id']);
            $table->dropColumn('kabupaten_id');

            $table->renameColumn('persentase_tarif_sektor', 'persentase_pengenaan_sektor');
            $table->text('alamat')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('nomor_kuitansi')->nullable();
            $table->decimal('pbbkb', 15, 2);
            $table->string('lokasi_penyaluran')->nullable()->comment('lokasi penyaluran: depot|TBBM');
            $table->boolean('is_wajib_pajak')->nullable()->comment('Status wajib pajak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->foreignId('kabupaten_id')->constrained('kabupatens');
            $table->renameColumn('persentase_pengenaan_sektor', 'persentase_tarif_sektor');

            $table->dropColumn('alamat');
            $table->dropColumn('tanggal');
            $table->dropColumn('nomor_kuitansi');
            $table->dropColumn('pbbkb');
            $table->dropColumn('lokasi_penyaluran');
            $table->dropColumn('is_wajib_pajak');
        });
    }
};
