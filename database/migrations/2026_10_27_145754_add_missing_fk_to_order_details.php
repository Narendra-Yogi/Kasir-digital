<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
        {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // Lewati jika FK sudah ada (misal instalasi baru memakai migrasi lama yang sudah diperbaiki)
        $fkExists = DB::table('information_schema.key_column_usage')
            ->where('table_schema', DB::raw('DATABASE()'))
            ->where('table_name', 'order_details')
            ->where('column_name', 'order_id')
            ->where('referenced_table_name', 'orders')
            ->exists();

        if ($fkExists) {
            return;
        }

        Schema::table('order_details', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
};
