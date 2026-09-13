<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom current_stock pada tabel ingredients.
     * Kolom ini melacak sisa stok bahan secara real-time.
     * Berkurang otomatis saat penjualan via POS, bertambah saat restok.
     */
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('current_stock', 12, 2)->default(0)->after('cost_per_unit');
        });

        // Set current_stock = purchase_quantity untuk data bahan yang sudah ada
        DB::table('ingredients')->whereNull('deleted_at')->update([
            'current_stock' => DB::raw('purchase_quantity'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('current_stock');
        });
    }
};
