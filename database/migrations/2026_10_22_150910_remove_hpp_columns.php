<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus kolom HPP dari tabel items dan order_details.
     * Fitur HPP tidak lagi digunakan pada mode sederhana.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('hpp');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('hpp')->default(0)->nullable()->after('price');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->integer('hpp')->default(0)->nullable()->after('price');
        });
    }
};
