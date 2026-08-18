<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom HPP ke tabel order_details sebagai snapshot historis.
     * Nilai HPP di-capture saat transaksi agar laporan lama tetap akurat
     * meskipun HPP item diperbarui di kemudian hari.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // HPP snapshot per unit saat transaksi terjadi
            $table->integer('hpp')->default(0)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });
    }
};
