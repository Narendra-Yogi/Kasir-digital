<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master daftar menu makanan dan minuman yang dijual di POS.
     */
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('kategori')->restrictOnDelete()->comment('Relasi ke tabel kategori');
            $table->string('name')->comment('Nama menu makanan/minuman');
            $table->unsignedBigInteger('price')->comment('Harga jual per porsi dalam Rupiah');
            $table->integer('stock')->default(0)->comment('Sisa stok porsi yang tersedia');
            $table->boolean('is_available')->default(true)->comment('Status ketersediaan di layar kasir');
            $table->string('image')->nullable()->comment('Path file foto menu di storage public');
            $table->foreignId('created_by')->nullable()->constrained('pengguna')->nullOnDelete()->comment('ID pengguna pembuat menu');
            $table->foreignId('updated_by')->nullable()->constrained('pengguna')->nullOnDelete()->comment('ID pengguna terakhir yang mengubah');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_available']);
            $table->index('stock');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};

