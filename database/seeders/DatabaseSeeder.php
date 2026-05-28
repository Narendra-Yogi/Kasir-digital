<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        // 1. Bikin Data User (Admin dan Kasir)
        User::create([
            'name' => 'Narendra Yogi', // Owner/Admin
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kasir Depan',
            'username' => 'kasir1',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);

        // 2. Bikin Data Kategori
        $kategoriPaket = Category::create(['name' => 'Paket Geprek']);
        $kategoriAlaCarte = Category::create(['name' => 'Ala Carte']);
        $kategoriMinuman = Category::create(['name' => 'Minuman']);
        $kategoriTopping = Category::create(['name' => 'Topping']);

        // 3. Bikin Data Master Menu (Items)
        // Menu Paket
        Item::create([
            'category_id' => $kategoriPaket->id,
            'name' => 'Paket Geprek Dada + Nasi',
            'price' => 20000,
        ]);
        Item::create([
            'category_id' => $kategoriPaket->id,
            'name' => 'Paket Geprek Paha + Nasi',
            'price' => 18000,
        ]);

        // Menu Ala Carte
        Item::create([
            'category_id' => $kategoriAlaCarte->id,
            'name' => 'Ayam Geprek Dada (Tanpa Nasi)',
            'price' => 16000,
        ]);

        // Menu Minuman
        Item::create([
            'category_id' => $kategoriMinuman->id,
            'name' => 'Es Teh Manis',
            'price' => 4000,
        ]);
        Item::create([
            'category_id' => $kategoriMinuman->id,
            'name' => 'Es Jeruk',
            'price' => 5000,
        ]);

        // Menu Topping
        Item::create([
            'category_id' => $kategoriTopping->id,
            'name' => 'Kol Goreng',
            'price' => 3000,
        ]);
        Item::create([
            'category_id' => $kategoriTopping->id,
            'name' => 'Tahu / Tempe',
            'price' => 2000,
        ]);
    }
}
