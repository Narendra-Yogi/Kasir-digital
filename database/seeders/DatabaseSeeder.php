<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pengeluaran;
use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. USER
        // ============================================================
        $admin = User::create([
            'name'     => 'Narendra Yogi',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
            'security_question' => 'Apa makanan favorit Anda?',
            'security_answer'   => Hash::make('geprek'),
        ]);

        $kasir1 = User::create([
            'name'     => 'Kasir Depan',
            'username' => 'kasir1',
            'password' => Hash::make('password123'),
            'role'     => 'kasir',
            'security_question' => 'Di kota mana Anda dilahirkan?',
            'security_answer'   => Hash::make('jakarta'),
        ]);

        // ============================================================
        // 2. KATEGORI
        // ============================================================
        $kategoriPaket    = Category::create(['name' => 'Paket Geprek']);
        $kategoriAlaCarte = Category::create(['name' => 'Ala Carte']);
        $kategoriMinuman  = Category::create(['name' => 'Minuman']);
        $kategoriTopping  = Category::create(['name' => 'Topping']);

        // ============================================================
        // 3. MENU ITEMS (dengan HPP)
        // ============================================================
        $items = [
            // [0] Paket Geprek Dada + Nasi — margin 45%
            Item::create([
                'category_id'  => $kategoriPaket->id,
                'name'         => 'Paket Geprek Dada + Nasi',
                'price'        => 20000,
                'hpp'          => 11000,
                'stock'        => 50,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [1] Paket Geprek Paha + Nasi — margin 47%
            Item::create([
                'category_id'  => $kategoriPaket->id,
                'name'         => 'Paket Geprek Paha + Nasi',
                'price'        => 18000,
                'hpp'          => 9500,
                'stock'        => 50,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [2] Paket Geprek 2 Paha + Nasi — margin 44%
            Item::create([
                'category_id'  => $kategoriPaket->id,
                'name'         => 'Paket Geprek 2 Paha + Nasi',
                'price'        => 25000,
                'hpp'          => 14000,
                'stock'        => 30,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [3] Ayam Geprek Dada (Tanpa Nasi) — margin 47%
            Item::create([
                'category_id'  => $kategoriAlaCarte->id,
                'name'         => 'Ayam Geprek Dada (Tanpa Nasi)',
                'price'        => 16000,
                'hpp'          => 8500,
                'stock'        => 40,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [4] Ayam Geprek Paha (Tanpa Nasi) — margin 50%
            Item::create([
                'category_id'  => $kategoriAlaCarte->id,
                'name'         => 'Ayam Geprek Paha (Tanpa Nasi)',
                'price'        => 14000,
                'hpp'          => 7000,
                'stock'        => 40,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [5] Nasi Putih — margin 60%
            Item::create([
                'category_id'  => $kategoriAlaCarte->id,
                'name'         => 'Nasi Putih',
                'price'        => 5000,
                'hpp'          => 2000,
                'stock'        => 100,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [6] Es Teh Manis — margin 70%
            Item::create([
                'category_id'  => $kategoriMinuman->id,
                'name'         => 'Es Teh Manis',
                'price'        => 4000,
                'hpp'          => 1200,
                'stock'        => 999,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [7] Es Jeruk — margin 67%
            Item::create([
                'category_id'  => $kategoriMinuman->id,
                'name'         => 'Es Jeruk',
                'price'        => 6000,
                'hpp'          => 2000,
                'stock'        => 999,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [8] Air Mineral — margin 50%
            Item::create([
                'category_id'  => $kategoriMinuman->id,
                'name'         => 'Air Mineral',
                'price'        => 3000,
                'hpp'          => 1500,
                'stock'        => 999,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [9] Es Teh Tawar — margin 75%
            Item::create([
                'category_id'  => $kategoriMinuman->id,
                'name'         => 'Es Teh Tawar',
                'price'        => 2000,
                'hpp'          => 500,
                'stock'        => 999,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [10] Kol Goreng — margin 73%
            Item::create([
                'category_id'  => $kategoriTopping->id,
                'name'         => 'Kol Goreng',
                'price'        => 3000,
                'hpp'          => 800,
                'stock'        => 200,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [11] Tahu / Tempe — margin 70%
            Item::create([
                'category_id'  => $kategoriTopping->id,
                'name'         => 'Tahu / Tempe',
                'price'        => 2000,
                'hpp'          => 600,
                'stock'        => 200,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
            // [12] Telur Ceplok — margin 50%
            Item::create([
                'category_id'  => $kategoriTopping->id,
                'name'         => 'Telur Ceplok',
                'price'        => 5000,
                'hpp'          => 2500,
                'stock'        => 50,
                'is_available' => true,
                'created_by'   => $admin->id,
            ]),
        ];

        // ============================================================
        // 4. TRANSAKSI — 14 hari terakhir, realistis untuk warung geprek
        // ============================================================
        $invoiceCounter = 1;

        // Variasi skenario pesanan per transaksi [item, qty]
        $skenarioHarian = [
            [[$items[0], 1], [$items[6], 1]],
            [[$items[1], 2], [$items[6], 2]],
            [[$items[3], 1], [$items[5], 1], [$items[7], 1]],
            [[$items[0], 1], [$items[12], 1], [$items[6], 1]],
            [[$items[2], 1], [$items[9], 1]],
            [[$items[1], 1], [$items[10], 1], [$items[6], 1]],
            [[$items[3], 2], [$items[11], 2], [$items[6], 2]],
            [[$items[4], 1], [$items[5], 1], [$items[8], 1]],
            [[$items[0], 1], [$items[1], 1], [$items[6], 2]],
            [[$items[2], 2], [$items[7], 2]],
            [[$items[0], 1], [$items[10], 1], [$items[9], 1]],
            [[$items[3], 1], [$items[12], 1], [$items[7], 1]],
        ];

        // Pengeluaran operasional harian
        $pengeluaranData = [
            ['item_name' => 'Beli ayam 5 kg',       'category' => 'bahan',    'amount' => 90000],
            ['item_name' => 'Beli beras 5 kg',       'category' => 'bahan',    'amount' => 70000],
            ['item_name' => 'Beli minyak goreng 2L', 'category' => 'bahan',    'amount' => 40000],
            ['item_name' => 'Beli teh & gula',       'category' => 'bahan',    'amount' => 25000],
            ['item_name' => 'Gas LPG 3 kg',          'category' => 'logistik', 'amount' => 22000],
            ['item_name' => 'Beli jeruk 2 kg',       'category' => 'bahan',    'amount' => 18000],
            ['item_name' => 'Plastik & styrofoam',   'category' => 'logistik', 'amount' => 15000],
            ['item_name' => 'Beli bumbu sambal',     'category' => 'bahan',    'amount' => 30000],
            ['item_name' => 'Bayar listrik',         'category' => 'lainnya',  'amount' => 50000],
            ['item_name' => 'Beli kol & sayuran',    'category' => 'bahan',    'amount' => 20000],
            ['item_name' => 'Beli tahu & tempe',     'category' => 'bahan',    'amount' => 35000],
            ['item_name' => 'Air mineral isi ulang', 'category' => 'logistik', 'amount' => 12000],
        ];

        for ($hari = 13; $hari >= 0; $hari--) {
            $tanggal = Carbon::today()->subDays($hari);
            $isWeekend = $tanggal->isWeekend();
            $kasirHariIni = ($hari % 3 === 0) ? $admin : $kasir1;

            // Weekend lebih ramai
            $jumlahTransaksi = $isWeekend ? rand(12, 18) : rand(7, 12);

            for ($t = 0; $t < $jumlahTransaksi; $t++) {
                $waktuTransaksi = $tanggal->copy()
                    ->setHour(rand(9, 20))
                    ->setMinute(rand(0, 59))
                    ->setSecond(rand(0, 59));

                $skenario = $skenarioHarian[array_rand($skenarioHarian)];

                // Hitung total transaksi
                $totalAmount = 0;
                foreach ($skenario as [$menuItem, $qty]) {
                    $totalAmount += $menuItem->price * $qty;
                }

                // 60% cash, 40% QRIS
                $paymentMethod = rand(1, 10) <= 6 ? 'cash' : 'qris';
                $cashReceived = $paymentMethod === 'cash'
                    ? $this->roundUpToCash($totalAmount)
                    : $totalAmount;

                // Sekitar 5% transaksi dibatalkan
                $status = rand(1, 20) === 1 ? 'cancelled' : 'success';

                $order = Order::create([
                    'invoice_number' => 'INV-' . $tanggal->format('ymd') . '-' . str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
                    'user_id'        => $kasirHariIni->id,
                    'customer_name'  => rand(0, 1) ? $this->namaAcak() : null,
                    'total_amount'   => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'cash_received'  => $cashReceived,
                    'change'         => $cashReceived - $totalAmount,
                    'status'         => $status,
                    'created_at'     => $waktuTransaksi,
                    'updated_at'     => $waktuTransaksi,
                ]);

                // Order details dengan snapshot HPP
                foreach ($skenario as [$menuItem, $qty]) {
                    OrderDetail::create([
                        'order_id'   => $order->id,
                        'item_id'    => $menuItem->id,
                        'quantity'   => $qty,
                        'price'      => $menuItem->price,
                        'hpp'        => $menuItem->hpp ?? 0,
                        'subtotal'   => $menuItem->price * $qty,
                        'notes'      => null,
                        'created_at' => $waktuTransaksi,
                        'updated_at' => $waktuTransaksi,
                    ]);
                }

                $invoiceCounter++;
            }

            // ============================================================
            // 5. PENGELUARAN HARIAN (2-4 item)
            // ============================================================
            $jumlahP = rand(2, 4);
            $pKeys   = array_rand($pengeluaranData, $jumlahP);
            if (!is_array($pKeys)) $pKeys = [$pKeys];

            foreach ($pKeys as $idx) {
                $p = $pengeluaranData[$idx];
                Pengeluaran::create([
                    'date'       => $tanggal->format('Y-m-d'),
                    'item_name'  => $p['item_name'],
                    'category'   => $p['category'],
                    'amount'     => max(5000, $p['amount'] + rand(-3000, 5000)),
                    'notes'      => null,
                    'created_by' => $admin->id,
                    'created_at' => $tanggal->copy()->setHour(8)->setMinute(0),
                    'updated_at' => $tanggal->copy()->setHour(8)->setMinute(0),
                ]);
            }

            // ============================================================
            // 6. INVENTORI HARIAN
            // ============================================================
            $invItems = [
                ['item_name' => 'Ayam (ekor)', 'old_stock' => rand(15, 25), 'sold' => rand(10, 20)],
                ['item_name' => 'Beras (kg)',   'old_stock' => rand(8, 15),  'sold' => rand(4, 8)],
                ['item_name' => 'Es Batu (kg)', 'old_stock' => rand(5, 10),  'sold' => rand(3, 7)],
            ];

            foreach ($invItems as $inv) {
                Inventory::create([
                    'date'            => $tanggal->format('Y-m-d'),
                    'item_name'       => $inv['item_name'],
                    'old_stock'       => $inv['old_stock'],
                    'new_stock'       => $inv['old_stock'],
                    'sold'            => $inv['sold'],
                    'remaining_stock' => max(0, $inv['old_stock'] - $inv['sold']),
                    'created_by'      => $admin->id,
                    'created_at'      => $tanggal->copy()->setHour(21)->setMinute(0),
                    'updated_at'      => $tanggal->copy()->setHour(21)->setMinute(0),
                ]);
            }
        }
    }

    /**
     * Bulatkan ke nominal kembalian yang realistis.
     */
    private function roundUpToCash(int $total): int
    {
        foreach ([2000, 5000, 10000, 20000, 50000, 100000] as $p) {
            $result = (int) ceil($total / $p) * $p;
            if ($result >= $total) return $result;
        }
        return $total;
    }

    /**
     * Nama pelanggan acak.
     */
    private function namaAcak(): string
    {
        $nama = ['Budi', 'Sari', 'Agus', 'Rina', 'Deni', 'Fitri', 'Hendra', 'Maya',
                 'Fajar', 'Dewi', 'Rudi', 'Sinta', 'Eko', 'Lina', 'Wahyu', 'Nisa',
                 'Bagas', 'Putri', 'Galih', 'Ayu', 'Pak RT', 'Bu Warni', 'Mas Ikhsan'];
        return $nama[array_rand($nama)];
    }
}
