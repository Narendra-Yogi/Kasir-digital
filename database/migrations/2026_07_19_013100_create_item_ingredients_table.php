<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel pivot item_ingredients (resep/komposisi bahan per produk).
     * Menghubungkan produk (items) dengan bahan baku (ingredients),
     * menyimpan jumlah bahan yang dibutuhkan per 1 porsi produk.
     */
    public function up(): void
    {
        Schema::create('item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_needed', 10, 2); // Jumlah bahan per 1 porsi (misal 250 gram ayam)
            $table->timestamps();

            // Satu produk tidak boleh punya bahan duplikat
            $table->unique(['item_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_ingredients');
    }
};
