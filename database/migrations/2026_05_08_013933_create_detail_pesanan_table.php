<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel rincian item yang dibeli pada setiap transaksi pesanan.
     */
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('pesanan')->cascadeOnDelete()->comment('Relasi ke tabel pesanan (header struk)');
            $table->foreignId('item_id')->constrained('menu')->restrictOnDelete()->comment('Relasi ke tabel menu yang terjual');
            $table->unsignedInteger('quantity')->comment('Jumlah porsi yang dibeli');
            $table->unsignedBigInteger('price')->comment('Snapshot harga satuan menu saat transaksi terjadi (Rupiah)');
            $table->unsignedBigInteger('subtotal')->comment('Total harga baris ini (quantity * price)');
            $table->string('notes')->nullable()->comment('Catatan khusus pesanan (contoh: pedas level 5)');
            $table->timestamps();

            $table->index(['order_id', 'item_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};

