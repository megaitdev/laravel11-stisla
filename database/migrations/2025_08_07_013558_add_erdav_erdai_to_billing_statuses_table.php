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
            $table->string('erdav')->nullable()->comment('Tanggal cetak OC (Order Confirmation)')->after('kunr'); // 'Tgl Cetak OC'
            $table->string('erdai')->nullable()->comment('Tanggal cetak billing')->after('erdav'); // 'Tgl Cetak Billing'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mak_sd')->table('billing_statuses', function (Blueprint $table) {
            $table->dropColumn(['erdav', 'erdai']);
        });
    }
};
