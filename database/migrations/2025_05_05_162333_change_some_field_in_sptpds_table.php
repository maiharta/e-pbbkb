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
        Schema::table('sptpds', function (Blueprint $table) {
            $table->dropColumn('wajib_pajak');
            $table->dropColumn('jabatan');

            $table->decimal('total_pbbkb', 15, 2)->nullable()->after('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sptpds', function (Blueprint $table) {
            $table->string('wajib_pajak')->nullable()->after('tanggal');
            $table->string('jabatan')->nullable()->after('wajib_pajak');
            $table->dropColumn('total_pbbkb');
        });
    }
};
