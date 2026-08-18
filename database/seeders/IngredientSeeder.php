<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Item;
use App\Models\ItemIngredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Seed data bahan baku dan resep untuk semua produk Geprek Legend.
     * Data ini berisi harga bahan yang realistis untuk warung ayam geprek.
     */
    public function run(): void
    {
        // Hapus data lama jika ada (agar bisa re-run)
        ItemIngredient::truncate();
        Ingredient::query()->forceDelete();

        // =============================================
        // 1. MASTER BAHAN BAKU
        // =============================================

        $ingredients = [
            // --- Bahan Utama ---
            [
                'name'              => 'Ayam Dada',
                'purchase_price'    => 38000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Ayam Paha',
                'purchase_price'    => 34000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Beras',
                'purchase_price'    => 14000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],

            // --- Bumbu & Tepung ---
            [
                'name'              => 'Tepung Terigu',
                'purchase_price'    => 12000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Tepung Bumbu',
                'purchase_price'    => 8000,
                'purchase_quantity' => 250,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Cabe Rawit',
                'purchase_price'    => 40000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Bawang Putih',
                'purchase_price'    => 30000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Bumbu Penyedap',
                'purchase_price'    => 25000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Garam',
                'purchase_price'    => 5000,
                'purchase_quantity' => 500,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],

            // --- Minyak ---
            [
                'name'              => 'Minyak Goreng',
                'purchase_price'    => 18000,
                'purchase_quantity' => 1000,
                'unit'              => 'ml',
                'created_by'        => 1,
            ],

            // --- Kemasan ---
            [
                'name'              => 'Styrofoam Box',
                'purchase_price'    => 15000,
                'purchase_quantity' => 50,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Plastik Kresek',
                'purchase_price'    => 10000,
                'purchase_quantity' => 100,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Sendok Plastik',
                'purchase_price'    => 8000,
                'purchase_quantity' => 100,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],

            // --- Bahan Minuman ---
            [
                'name'              => 'Teh Celup',
                'purchase_price'    => 10000,
                'purchase_quantity' => 25,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Gula Pasir',
                'purchase_price'    => 15000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Jeruk Peras',
                'purchase_price'    => 20000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Es Batu',
                'purchase_price'    => 5000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Gelas Plastik + Tutup',
                'purchase_price'    => 20000,
                'purchase_quantity' => 50,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Sedotan',
                'purchase_price'    => 5000,
                'purchase_quantity' => 100,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Air Mineral Botol',
                'purchase_price'    => 36000,
                'purchase_quantity' => 24,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],

            // --- Bahan Topping ---
            [
                'name'              => 'Kol',
                'purchase_price'    => 8000,
                'purchase_quantity' => 1000,
                'unit'              => 'gram',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Tahu',
                'purchase_price'    => 5000,
                'purchase_quantity' => 10,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Tempe',
                'purchase_price'    => 4000,
                'purchase_quantity' => 10,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
            [
                'name'              => 'Telur Ayam',
                'purchase_price'    => 28000,
                'purchase_quantity' => 15,
                'unit'              => 'pcs',
                'created_by'        => 1,
            ],
        ];

        // Simpan semua bahan baku
        $savedIngredients = [];
        foreach ($ingredients as $data) {
            $ing = Ingredient::create($data);
            $savedIngredients[$data['name']] = $ing->id;
        }

        // =============================================
        // 2. RESEP BAHAN PER PRODUK (disesuaikan agar margin realistis)
        // =============================================

        $recipes = [
            // --- ID 1: Paket Geprek Dada + Nasi (Harga: 20.000) ---
            // Target HPP: ~12.000 (margin ~40%)
            1 => [
                ['name' => 'Ayam Dada',       'qty' => 150],   // Rp 5.700
                ['name' => 'Tepung Terigu',    'qty' => 50],    // Rp 600
                ['name' => 'Tepung Bumbu',     'qty' => 20],    // Rp 640
                ['name' => 'Cabe Rawit',       'qty' => 40],    // Rp 1.600
                ['name' => 'Bawang Putih',     'qty' => 10],    // Rp 300
                ['name' => 'Bumbu Penyedap',   'qty' => 3],     // Rp 75
                ['name' => 'Garam',            'qty' => 2],     // Rp 20
                ['name' => 'Minyak Goreng',    'qty' => 80],    // Rp 1.440
                ['name' => 'Beras',            'qty' => 100],   // Rp 1.400
                ['name' => 'Styrofoam Box',    'qty' => 1],     // Rp 300
                ['name' => 'Plastik Kresek',   'qty' => 1],     // Rp 100
                ['name' => 'Sendok Plastik',   'qty' => 1],     // Rp 80
            ],
            // Total HPP ≈ Rp 12.255

            // --- ID 2: Paket Geprek Paha + Nasi (Harga: 18.000) ---
            // Target HPP: ~10.500 (margin ~42%)
            2 => [
                ['name' => 'Ayam Paha',        'qty' => 130],   // Rp 4.420
                ['name' => 'Tepung Terigu',    'qty' => 45],    // Rp 540
                ['name' => 'Tepung Bumbu',     'qty' => 18],    // Rp 576
                ['name' => 'Cabe Rawit',       'qty' => 40],    // Rp 1.600
                ['name' => 'Bawang Putih',     'qty' => 10],    // Rp 300
                ['name' => 'Bumbu Penyedap',   'qty' => 3],     // Rp 75
                ['name' => 'Garam',            'qty' => 2],     // Rp 20
                ['name' => 'Minyak Goreng',    'qty' => 70],    // Rp 1.260
                ['name' => 'Beras',            'qty' => 100],   // Rp 1.400
                ['name' => 'Styrofoam Box',    'qty' => 1],     // Rp 300
                ['name' => 'Plastik Kresek',   'qty' => 1],     // Rp 100
                ['name' => 'Sendok Plastik',   'qty' => 1],     // Rp 80
            ],
            // Total HPP ≈ Rp 10.671

            // --- ID 3: Paket Geprek 2 Paha + Nasi (Harga: 25.000) ---
            // Target HPP: ~15.000 (margin ~40%)
            3 => [
                ['name' => 'Ayam Paha',        'qty' => 260],   // Rp 8.840
                ['name' => 'Tepung Terigu',    'qty' => 80],    // Rp 960
                ['name' => 'Tepung Bumbu',     'qty' => 30],    // Rp 960
                ['name' => 'Cabe Rawit',       'qty' => 50],    // Rp 2.000
                ['name' => 'Bawang Putih',     'qty' => 12],    // Rp 360
                ['name' => 'Bumbu Penyedap',   'qty' => 5],     // Rp 125
                ['name' => 'Garam',            'qty' => 3],     // Rp 30
                ['name' => 'Minyak Goreng',    'qty' => 100],   // Rp 1.800
                ['name' => 'Beras',            'qty' => 120],   // Rp 1.680
                ['name' => 'Styrofoam Box',    'qty' => 1],     // Rp 300
                ['name' => 'Plastik Kresek',   'qty' => 1],     // Rp 100
                ['name' => 'Sendok Plastik',   'qty' => 1],     // Rp 80
            ],
            // Total HPP ≈ Rp 17.235 (margin ~31%)

            // --- ID 4: Ayam Geprek Dada Tanpa Nasi (Harga: 16.000) ---
            // Target HPP: ~9.500 (margin ~41%)
            4 => [
                ['name' => 'Ayam Dada',        'qty' => 150],   // Rp 5.700
                ['name' => 'Tepung Terigu',    'qty' => 50],    // Rp 600
                ['name' => 'Tepung Bumbu',     'qty' => 20],    // Rp 640
                ['name' => 'Cabe Rawit',       'qty' => 40],    // Rp 1.600
                ['name' => 'Bawang Putih',     'qty' => 10],    // Rp 300
                ['name' => 'Bumbu Penyedap',   'qty' => 3],     // Rp 75
                ['name' => 'Garam',            'qty' => 2],     // Rp 20
                ['name' => 'Minyak Goreng',    'qty' => 80],    // Rp 1.440
                ['name' => 'Styrofoam Box',    'qty' => 1],     // Rp 300
                ['name' => 'Plastik Kresek',   'qty' => 1],     // Rp 100
            ],
            // Total HPP ≈ Rp 10.775 (margin ~33%)

            // --- ID 5: Ayam Geprek Paha Tanpa Nasi (Harga: 14.000) ---
            // Target HPP: ~8.000 (margin ~43%)
            5 => [
                ['name' => 'Ayam Paha',        'qty' => 130],   // Rp 4.420
                ['name' => 'Tepung Terigu',    'qty' => 45],    // Rp 540
                ['name' => 'Tepung Bumbu',     'qty' => 18],    // Rp 576
                ['name' => 'Cabe Rawit',       'qty' => 40],    // Rp 1.600
                ['name' => 'Bawang Putih',     'qty' => 10],    // Rp 300
                ['name' => 'Bumbu Penyedap',   'qty' => 3],     // Rp 75
                ['name' => 'Garam',            'qty' => 2],     // Rp 20
                ['name' => 'Minyak Goreng',    'qty' => 70],    // Rp 1.260
                ['name' => 'Styrofoam Box',    'qty' => 1],     // Rp 300
                ['name' => 'Plastik Kresek',   'qty' => 1],     // Rp 100
            ],
            // Total HPP ≈ Rp 9.191 (margin ~34%)

            // --- ID 6: Nasi Putih (Harga: 5.000) ---
            6 => [
                ['name' => 'Beras',            'qty' => 100],   // Rp 1.400
                ['name' => 'Styrofoam Box',    'qty' => 1],     // Rp 300
            ],
            // Total HPP ≈ Rp 1.700 (margin ~66%)

            // --- ID 7: Es Teh Manis (Harga: 4.000) ---
            7 => [
                ['name' => 'Teh Celup',             'qty' => 1],     // Rp 400
                ['name' => 'Gula Pasir',             'qty' => 30],    // Rp 450
                ['name' => 'Es Batu',                'qty' => 100],   // Rp 500
                ['name' => 'Gelas Plastik + Tutup',  'qty' => 1],     // Rp 400
                ['name' => 'Sedotan',                'qty' => 1],     // Rp 50
            ],
            // Total HPP ≈ Rp 1.800 (margin ~55%)

            // --- ID 8: Es Jeruk (Harga: 6.000) ---
            8 => [
                ['name' => 'Jeruk Peras',            'qty' => 80],    // Rp 1.600
                ['name' => 'Gula Pasir',             'qty' => 25],    // Rp 375
                ['name' => 'Es Batu',                'qty' => 100],   // Rp 500
                ['name' => 'Gelas Plastik + Tutup',  'qty' => 1],     // Rp 400
                ['name' => 'Sedotan',                'qty' => 1],     // Rp 50
            ],
            // Total HPP ≈ Rp 2.925 (margin ~51%)

            // --- ID 9: Air Mineral (Harga: 3.000) ---
            9 => [
                ['name' => 'Air Mineral Botol',  'qty' => 1],   // Rp 1.500
            ],
            // Total HPP = Rp 1.500 (margin 50%)

            // --- ID 10: Es Teh Tawar (Harga: 2.000) ---
            10 => [
                ['name' => 'Teh Celup',             'qty' => 1],     // Rp 400
                ['name' => 'Es Batu',                'qty' => 100],   // Rp 500
                ['name' => 'Gelas Plastik + Tutup',  'qty' => 1],     // Rp 400
                ['name' => 'Sedotan',                'qty' => 1],     // Rp 50
            ],
            // Total HPP ≈ Rp 1.350 (margin ~33%)

            // --- ID 11: Kol Goreng (Harga: 3.000) ---
            11 => [
                ['name' => 'Kol',              'qty' => 80],    // Rp 640
                ['name' => 'Minyak Goreng',    'qty' => 20],    // Rp 360
                ['name' => 'Bumbu Penyedap',   'qty' => 2],     // Rp 50
                ['name' => 'Garam',            'qty' => 2],     // Rp 20
            ],
            // Total HPP ≈ Rp 1.070 (margin ~64%)

            // --- ID 12: Tahu / Tempe (Harga: 2.000) ---
            12 => [
                ['name' => 'Tahu',             'qty' => 1],     // Rp 500
                ['name' => 'Tempe',            'qty' => 1],     // Rp 400
                ['name' => 'Minyak Goreng',    'qty' => 20],    // Rp 360
            ],
            // Total HPP ≈ Rp 1.260 (margin ~37%)

            // --- ID 13: Telur Ceplok (Harga: 5.000) ---
            13 => [
                ['name' => 'Telur Ayam',       'qty' => 1],     // Rp 1.867
                ['name' => 'Minyak Goreng',    'qty' => 15],    // Rp 270
            ],
            // Total HPP ≈ Rp 2.137 (margin ~57%)
        ];

        // Simpan resep dan hitung ulang HPP untuk setiap item
        foreach ($recipes as $itemId => $ingredientList) {
            $item = Item::find($itemId);
            if (!$item) continue;

            foreach ($ingredientList as $ing) {
                if (isset($savedIngredients[$ing['name']])) {
                    ItemIngredient::create([
                        'item_id'         => $itemId,
                        'ingredient_id'   => $savedIngredients[$ing['name']],
                        'quantity_needed' => $ing['qty'],
                    ]);
                }
            }

            // Recalculate HPP otomatis dari resep
            $item->recalculateHpp();
        }
    }
}
