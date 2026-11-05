<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel header transaksi pesanan penjualan di kasir (struk/invoice).
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->comment('Nomor struk/invoice unik (contoh: INV-260508-0001)');
            $table->foreignId('user_id')->constrained('pengguna')->restrictOnDelete()->comment('ID kasir/admin yang melayani transaksi');
            $table->string('customer_name')->nullable()->comment('Nama pelanggan (opsional)');
            $table->unsignedBigInteger('total_amount')->comment('Total tagihan pesanan dalam Rupiah');
            $table->enum('payment_method', ['cash', 'qris'])->comment('Metode pembayaran: tunai (cash) atau QRIS');
            $table->unsignedBigInteger('cash_received')->nullable()->comment('Nominal uang yang diterima dari pelanggan');
            $table->unsignedBigInteger('change')->default(0)->comment('Nominal uang kembalian dalam Rupiah');
            $table->enum('status', ['success', 'cancelled'])->default('success')->comment('Status pesanan: berhasil atau dibatalkan');
            $table->timestamps();

            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index(['status', 'payment_method', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};

