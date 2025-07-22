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
        // Specify the 'mak_sd' database connection and create the 'billing_status' table
        Schema::connection('mak_sd')->create('billing_statuses', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('weeks')->nullable()->comment('Minggu ke- berapa data ini dicatat'); // 'Week'
            $table->string('vtweg')->nullable()->comment('Saluran distribusi produk'); // 'Dist. chanel'
            $table->string('bldat')->nullable()->comment('Tanggal surat jalan dikeluarkan'); // 'Tgl Surat Jalan'
            $table->string('tglgi')->nullable()->comment('Tanggal Goods Issue (barang keluar gudang)'); // 'Tgl GI'
            $table->string('lfart')->nullable()->comment('Jenis atau tipe dokumen pengiriman'); // 'Type'
            $table->string('mblnr')->nullable()->comment('Nomor dokumen Delivery Order (DO)'); // 'No. Dok DO'); // Kriteria Upsert 1
            $table->string('vbeln')->nullable()->comment('Nomor surat jalan atau dokumen pengiriman'); // 'No. Surat Jalan'
            $table->string('vgbel')->nullable()->comment('Nomor Sales Order (SO) terkait'); // 'No. SO'); // Kriteria Upsert 2
            $table->string('name1')->nullable()->comment('Nama pihak yang menjual atau pihak penjual utama'); // 'Sold To Party'
            $table->string('name2')->nullable()->comment('Nama pihak penerima barang'); // 'Ship to Party'
            $table->text('notes')->nullable()->comment('Catatan atau informasi tambahan (ditingkatkan menjadi TEXT)'); // 'Notes'
            $table->string('fkdat')->nullable()->comment('Tanggal kwitansi atau faktur dikeluarkan'); // 'Tgl Kwitansi'
            $table->string('vbelm')->nullable()->comment('Nomor kwitansi atau dokumen billing'); // 'No. Kwitansi'
            $table->string('stb')->nullable()->comment('Status Barang Jadi (Finished Goods)'); // 'STB FG'
            $table->string('stbk')->nullable()->comment('Status komponen produk'); // 'STB Komponen'
            $table->string('hna')->nullable()->comment('Harga Nett Awal'); // 'HNA'
            $table->string('tdisc')->nullable()->comment('Jumlah diskon yang diberikan'); // 'Disc'
            $table->string('nett')->nullable()->comment('Jumlah bersih setelah diskon'); // 'Nett'
            $table->string('lfstk')->nullable()->comment('Status keseluruhan pengiriman'); // 'Status Delivery'
            $table->string('vdatu')->nullable()->comment('Tanggal pengiriman yang diminta'); // 'Req Deliv date'
            $table->string('zterm')->nullable()->comment('Syarat pembayaran yang disepakati'); // 'Term of Payment'
            $table->string('fkstk')->nullable()->comment('Status keseluruhan proses billing/penagihan'); // 'Status Billing'
            $table->string('kunr')->nullable()->comment('ID unik untuk pelanggan'); // 'ID Customer'
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mak_sd')->dropIfExists('billing_statuses');
    }
};
