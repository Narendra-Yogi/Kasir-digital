<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom HPP (Harga Pokok Penjualan) ke tabel items.
     * HPP adalah harga modal/biaya produksi per unit item.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // HPP ditambahkan setelah kolom 'price', nullable agar item lama tidak rusak
            $table->integer('hpp')->default(0)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });
    }
};
