<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buku_kas_harian', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique(); // Hanya boleh 1 laporan tutup kasir per tanggal
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kasir yang bertugas menutup
            $table->decimal('starting_cash', 12, 2)->default(0); // Modal Awal
            $table->decimal('system_cash_sales', 12, 2)->default(0); // Pemasukan Tunai Sistem
            $table->decimal('system_qris_sales', 12, 2)->default(0); // Pemasukan QRIS Sistem
            $table->decimal('system_expenses', 12, 2)->default(0); // Pengeluaran Sistem
            $table->decimal('actual_cash', 12, 2); // Uang Fisik Aktual di Laci
            $table->decimal('discrepancy', 12, 2); // Selisih Kas
            $table->text('notes')->nullable(); // Catatan Kejadian Kasir
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
