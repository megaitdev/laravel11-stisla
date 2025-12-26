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
        Schema::connection('mak_pch')->create('sap_purchasing_lists', function (Blueprint $table) {
            $table->id();

            // Vendor Information
            $table->string('ifnr', 50)->nullable()->comment('Vendor ID');
            $table->string('bsart', 10)->nullable()->comment('Document Type');
            $table->string('name1', 255)->nullable()->comment('Vendor Name');

            // Date Fields
            $table->date('bedat')->nullable()->comment('PO Date');
            $table->date('eindt')->nullable()->comment('Delivery Date');

            // Time/Counter Fields
            $table->integer('tim')->default(0)->comment('Time/Counter');
            $table->integer('tima')->default(0)->comment('Time/Counter Alt');

            // Reference Fields
            $table->string('verkf', 100)->nullable()->comment('Salesperson');
            $table->char('frgke', 1)->nullable()->comment('Release Indicator');
            $table->string('ktlog', 50)->nullable()->comment('Catalog Type');

            // Purchase Order Fields
            $table->string('ebeln', 20)->nullable()->index()->comment('PO Number (Primary Index)');
            $table->string('ebelp', 10)->nullable()->comment('PO Item Number');
            $table->string('banfn', 20)->nullable()->comment('Purchase Requisition');
            $table->string('bednr', 20)->nullable()->comment('Tracking Number');

            // Material Fields
            $table->string('matnr', 40)->nullable()->comment('Material Number');
            $table->string('txz01', 255)->nullable()->comment('Short Text/Description');

            // Quantity Fields
            $table->integer('menge')->default(0)->comment('Quantity');
            $table->integer('menga')->default(0)->comment('Quantity Alt');

            // Value Field (precision 15, scale 2)
            $table->decimal('netwr', 15, 2)->default(0)->comment('Net Value');

            // Document Fields
            $table->string('mblnr', 20)->nullable()->comment('Material Doc Number');
            $table->date('budat')->nullable()->comment('Posting Date');

            // Status Fields
            $table->boolean('is_open')->default(true)->comment('Open Status');

            $table->timestamps();

            // Composite Index for faster queries
            $table->index(['bedat', 'eindt'], 'idx_sap_purchasing_dates');
            $table->index(['ifnr', 'ebeln'], 'idx_sap_purchasing_vendor_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mak_sd')->dropIfExists('sap_purchasing_lists');
    }
};

