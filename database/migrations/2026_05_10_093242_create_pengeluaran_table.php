<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel catatan pengeluaran operasional & belanja harian toko.
     */
    public function up(): void
    {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('Tanggal pengeluaran');
            $table->string('item_name')->comment('Nama barang/keperluan pengeluaran');
            $table->enum('category', ['belanja_harian', 'operasional', 'gaji', 'lainnya'])
                ->default('belanja_harian')
                ->comment('Kategori pengeluaran usaha');
            $table->unsignedBigInteger('amount')->comment('Nominal pengeluaran dalam Rupiah');
            $table->string('notes')->nullable()->comment('Keterangan tambahan (opsional)');
            $table->foreignId('created_by')->constrained('pengguna')->restrictOnDelete()->comment('ID pengguna pencatat pengeluaran');
            $table->foreignId('updated_by')->nullable()->constrained('pengguna')->nullOnDelete()->comment('ID pengguna terakhir yang mengubah');
            $table->timestamps();

            $table->index('date');
            $table->index(['date', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};

