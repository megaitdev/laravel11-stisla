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
        Schema::connection('mak_sd')->create('billing_notes', function (Blueprint $table) {
            $table->id();
            $table->string('vbeln')->comment('Nomor surat jalan atau dokumen pengiriman');
            $table->string('status')->comment('Status billing terkait catatan ini');
            $table->text('note')->nullable()->comment('Catatan tambahan terkait billing');
            $table->string('created_by')->nullable()->comment('User yang membuat catatan');
            $table->string('updated_by')->nullable()->comment('User yang mengupdate catatan');
            $table->timestamps();
            $table->foreign('vbeln')->references('vbeln')->on('billing_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mak_sd')->dropIfExists('billing_notes');
    }
};
