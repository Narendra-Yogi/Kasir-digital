<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder untuk reset seluruh data transaksional dan master data,
 * KECUALI akun user yang sudah ada (admin & kasir).
 *
 * Jalankan: php artisan db:seed --class=ResetAllDataSeeder
 */
class ResetAllDataSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: hapus tabel child dulu, lalu parent, agar tidak kena constraint FK.
        Schema::disableForeignKeyConstraints();

        $tables = [
            // Child tables dulu, lalu parent
            'order_details',
            'orders',
            'pengeluaran',
            'daily_cash_logs',
            'items',
            'categories',
            'sessions',
            'cache',
            'cache_locks',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->info("   Truncated: {$table}");
            }
        }

        Schema::enableForeignKeyConstraints();

        // Bersihkan Laravel application cache (agar data POS yang di-cache juga hilang)
        Artisan::call('cache:clear');

        $this->command->info('✅ Semua data berhasil di-reset! Akun user (admin & kasir) tetap aman.');
        $this->command->info('   Silakan login kembali dan mulai input data baru.');
    }
}
