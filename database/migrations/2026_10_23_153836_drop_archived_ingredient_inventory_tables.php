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
        // 1. Drop tabel pivot item_ingredients terlebih dahulu karena memiliki foreign key ke ingredients & items
        Schema::dropIfExists('item_ingredients');

        // 2. Drop foreign key dan kolom ingredient_id dari tabel pengeluaran
        if (Schema::hasColumn('pengeluaran', 'ingredient_id')) {
            Schema::table('pengeluaran', function (Blueprint $table) {
                $table->dropForeign(['ingredient_id']);
                $table->dropColumn('ingredient_id');
            });
        }

        // 3. Drop tabel utama ingredients & inventories
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('inventories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recreate tabel ingredients
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('purchase_price');
            $table->decimal('purchase_quantity', 10, 2);
            $table->string('unit', 20);
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
        });

        // 2. Recreate tabel inventories
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('item_name');
            $table->integer('old_stock')->default(0);
            $table->integer('new_stock')->default(0);
            $table->integer('sold')->default(0);
            $table->integer('remaining_stock')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Recreate tabel pivot item_ingredients
        Schema::create('item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_needed', 10, 2);
            $table->string('unit_used', 20)->nullable();
            $table->timestamps();
            $table->unique(['item_id', 'ingredient_id']);
        });

        // 4. Recreate kolom ingredient_id pada tabel pengeluaran
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->nullable()->after('created_by')
                ->constrained()->nullOnDelete();
        });
    }
};
