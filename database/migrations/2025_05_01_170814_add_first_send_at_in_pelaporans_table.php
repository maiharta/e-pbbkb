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
        Schema::table('pelaporans', function (Blueprint $table) {
            $table->timestamp('first_send_at')->nullable()->after('is_sent_to_admin');
            $table->boolean(('is_expired'))->default(false)->after('first_send_at');
            $table->boolean('is_paid')->default(false)->after('is_expired');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelaporans', function (Blueprint $table) {
            $table->dropColumn('first_send_at');
            $table->dropColumn('is_expired');
            $table->dropColumn('is_paid');
            $table->dropSoftDeletes();
        });
    }
};
