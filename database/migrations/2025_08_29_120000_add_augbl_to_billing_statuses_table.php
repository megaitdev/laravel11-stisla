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
        Schema::connection('mak_sd')->table('billing_statuses', function (Blueprint $table) {
            // SAP: AUGBL = Accounting Document Number
            $table->string('augbl')->nullable()->comment('Nomor dokumen akuntansi (AUGBL)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mak_sd')->table('billing_statuses', function (Blueprint $table) {
            $table->dropColumn('augbl');
        });
    }
};
