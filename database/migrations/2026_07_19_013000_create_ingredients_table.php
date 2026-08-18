<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel master bahan baku (ingredients).
     * Menyimpan data bahan-bahan yang digunakan untuk produksi menu,
     * seperti cabe, ayam, tepung, minyak, styrofoam, plastik, dll.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nama bahan: Cabe Rawit, Tepung Terigu, dll
            $table->integer('purchase_price');                // Harga beli total (Rp 40.000)
            $table->decimal('purchase_quantity', 10, 2);     // Jumlah per pembelian (1000 gram, 50 pcs)
            $table->string('unit', 20);                      // Satuan: gram, ml, pcs, kg, liter
            $table->decimal('cost_per_unit', 10, 2)->default(0); // Harga per satuan terkecil (otomatis dihitung)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
