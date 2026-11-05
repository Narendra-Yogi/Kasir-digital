<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel rekapitulasi tutup buku kas harian (rekonsiliasi uang laci kasir).
     */
    public function up(): void
    {
        Schema::create('buku_kas_harian', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->comment('Tanggal tutup buku (1 laporan per hari)');
            $table->foreignId('user_id')->constrained('pengguna')->restrictOnDelete()->comment('ID kasir/admin yang menutup buku kas');
            $table->unsignedBigInteger('starting_cash')->default(0)->comment('Modal uang tunai awal di laci (Rupiah)');
            $table->unsignedBigInteger('system_cash_sales')->default(0)->comment('Total penjualan tunai tercatat di sistem (Rupiah)');
            $table->unsignedBigInteger('system_qris_sales')->default(0)->comment('Total penjualan QRIS tercatat di sistem (Rupiah)');
            $table->unsignedBigInteger('system_expenses')->default(0)->comment('Total pengeluaran toko hari ini di sistem (Rupiah)');
            $table->unsignedBigInteger('actual_cash')->comment('Uang tunai fisik aktual yang dihitung di laci (Rupiah)');
            $table->bigInteger('discrepancy')->comment('Selisih kas (actual_cash - uang kas sistem, bisa negatif/positif)');
            $table->text('notes')->nullable()->comment('Catatan kejadian kasir saat tutup buku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_kas_harian');
    }
};

