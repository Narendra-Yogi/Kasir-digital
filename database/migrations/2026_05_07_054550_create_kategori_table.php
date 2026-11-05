<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel master kategori menu (contoh: Paket Geprek, Ala Carte, Minuman, Topping).
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama kategori menu');
            $table->foreignId('created_by')->nullable()->constrained('pengguna')->nullOnDelete()->comment('ID pengguna pembuat kategori');
            $table->foreignId('updated_by')->nullable()->constrained('pengguna')->nullOnDelete()->comment('ID pengguna terakhir yang mengubah');
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};

