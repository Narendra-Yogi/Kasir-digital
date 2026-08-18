<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom ingredient_id ke tabel pengeluaran.
     * Kolom ini menandai bahwa record pengeluaran dihasilkan secara otomatis
     * dari input bahan baku (ingredients), sehingga user tidak perlu
     * mencatat pengeluaran bahan secara manual (double input).
     */
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->nullable()->after('created_by')
                ->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn('ingredient_id');
        });
    }
};
