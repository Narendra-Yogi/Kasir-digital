<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mode sederhana UMKM: kategori baru + buka kunci pengeluaran auto dari bahan.
     * Non-destruktif: tidak drop tabel/kolom HPP di tahap ini (diarsipkan di UI saja).
     */
    public function up(): void
    {
        // 1. Longgarkan enum agar mencakup kategori lama + baru (MySQL).
        //    SQLite (testing) tidak mendukung MODIFY, jadi lewati untuk driver non-mysql.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `pengeluaran` MODIFY `category` ENUM('bahan','logistik','lainnya','belanja_harian','operasional','gaji') NOT NULL DEFAULT 'lainnya'"
            );
        }

        // 2. Migrasi data kategori lama ke baru.
        DB::table('pengeluaran')->where('category', 'bahan')->update(['category' => 'belanja_harian']);
        DB::table('pengeluaran')->where('category', 'logistik')->update(['category' => 'operasional']);

        // 3. Buka kunci pengeluaran auto dari bahan: jadikan manual biasa.
        //    Tandai notes agar jejak audit tetap ada.
        $autoRows = DB::table('pengeluaran')->whereNotNull('ingredient_id')->get();
        foreach ($autoRows as $row) {
            $oldNotes = $row->notes ? $row->notes.' ' : '';
            DB::table('pengeluaran')->where('id', $row->id)->update([
                'ingredient_id' => null,
                'notes' => trim($oldNotes.'[arsip auto bahan]'),
            ]);
        }

        // 4. Kunci enum ke kategori final v2.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `pengeluaran` MODIFY `category` ENUM('belanja_harian','operasional','gaji','lainnya') NOT NULL DEFAULT 'lainnya'"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `pengeluaran` MODIFY `category` ENUM('bahan','logistik','lainnya','belanja_harian','operasional','gaji') NOT NULL DEFAULT 'bahan'"
            );
        }

        DB::table('pengeluaran')->where('category', 'belanja_harian')->update(['category' => 'bahan']);
        DB::table('pengeluaran')->where('category', 'operasional')->update(['category' => 'logistik']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `pengeluaran` MODIFY `category` ENUM('bahan','logistik','lainnya') NOT NULL DEFAULT 'bahan'"
            );
        }
    }
};
