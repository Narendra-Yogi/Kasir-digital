<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom unit_used pada tabel item_ingredients.
     * Kolom ini menyimpan satuan yang dipilih user saat input resep,
     * yang mungkin berbeda dari satuan asli bahan baku.
     * Contoh: bahan disimpan dalam "kg" tapi resep di-input dalam "gram".
     * Jika null, berarti menggunakan satuan asli bahan.
     */
    public function up(): void
    {
        Schema::table('item_ingredients', function (Blueprint $table) {
            $table->string('unit_used', 20)->nullable()->after('quantity_needed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_ingredients', function (Blueprint $table) {
            $table->dropColumn('unit_used');
        });
    }
};
