<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pengeluaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. USER
        // ============================================================
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Narendra Yogi',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'security_question' => 'Apa makanan favorit Anda?',
                'security_answer' => Hash::make('geprek'),
            ]
        );

        $kasir1 = User::firstOrCreate(
            ['username' => 'kasir1'],
            [
                'name' => 'Kasir Depan',
                'password' => Hash::make('password123'),
                'role' => 'kasir',
                'security_question' => 'Di kota mana Anda dilahirkan?',
                'security_answer' => Hash::make('jakarta'),
            ]
        );

        // ============================================================
        // 2. KATEGORI
        // ============================================================
        $kategoriPaket = Category::firstOrCreate(['name' => 'Paket Geprek']);
        $kategoriAlaCarte = Category::firstOrCreate(['name' => 'Ala Carte']);
        $kategoriMinuman = Category::firstOrCreate(['name' => 'Minuman']);
        $kategoriTopping = Category::firstOrCreate(['name' => 'Topping']);

        // ============================================================
        // 3. MENU ITEMS
        // ============================================================
        $items = [
            // [0] Paket Geprek Dada + Nasi
            Item::firstOrCreate(
                ['name' => 'Paket Geprek Dada + Nasi'],
                [
                    'category_id' => $kategoriPaket->id,
                    'price' => 20000,
                    'stock' => 50,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [1] Paket Geprek Paha + Nasi
            Item::firstOrCreate(
                ['name' => 'Paket Geprek Paha + Nasi'],
                [
                    'category_id' => $kategoriPaket->id,
                    'price' => 18000,
                    'stock' => 50,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [2] Paket Geprek 2 Paha + Nasi
            Item::firstOrCreate(
                ['name' => 'Paket Geprek 2 Paha + Nasi'],
                [
                    'category_id' => $kategoriPaket->id,
                    'price' => 25000,
                    'stock' => 30,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [3] Ayam Geprek Dada (Tanpa Nasi)
            Item::firstOrCreate(
                ['name' => 'Ayam Geprek Dada (Tanpa Nasi)'],
                [
                    'category_id' => $kategoriAlaCarte->id,
                    'price' => 16000,
                    'stock' => 40,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [4] Ayam Geprek Paha (Tanpa Nasi)
            Item::firstOrCreate(
                ['name' => 'Ayam Geprek Paha (Tanpa Nasi)'],
                [
                    'category_id' => $kategoriAlaCarte->id,
                    'price' => 14000,
                    'stock' => 40,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [5] Nasi Putih
            Item::firstOrCreate(
                ['name' => 'Nasi Putih'],
                [
                    'category_id' => $kategoriAlaCarte->id,
                    'price' => 5000,
                    'stock' => 100,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [6] Es Teh Manis
            Item::firstOrCreate(
                ['name' => 'Es Teh Manis'],
                [
                    'category_id' => $kategoriMinuman->id,
                    'price' => 4000,
                    'stock' => 999,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [7] Es Jeruk
            Item::firstOrCreate(
                ['name' => 'Es Jeruk'],
                [
                    'category_id' => $kategoriMinuman->id,
                    'price' => 6000,
                    'stock' => 999,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [8] Air Mineral
            Item::firstOrCreate(
                ['name' => 'Air Mineral'],
                [
                    'category_id' => $kategoriMinuman->id,
                    'price' => 3000,
                    'stock' => 999,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [9] Es Teh Tawar
            Item::firstOrCreate(
                ['name' => 'Es Teh Tawar'],
                [
                    'category_id' => $kategoriMinuman->id,
                    'price' => 2000,
                    'stock' => 999,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [10] Kol Goreng
            Item::firstOrCreate(
                ['name' => 'Kol Goreng'],
                [
                    'category_id' => $kategoriTopping->id,
                    'price' => 3000,
                    'stock' => 200,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [11] Tahu / Tempe
            Item::firstOrCreate(
                ['name' => 'Tahu / Tempe'],
                [
                    'category_id' => $kategoriTopping->id,
                    'price' => 2000,
                    'stock' => 200,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
            // [12] Telur Ceplok
            Item::firstOrCreate(
                ['name' => 'Telur Ceplok'],
                [
                    'category_id' => $kategoriTopping->id,
                    'price' => 5000,
                    'stock' => 50,
                    'is_available' => true,
                    'created_by' => $admin->id,
                ]
            ),
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
            ['item_name' => 'Beli ayam 5 kg',       'category' => 'belanja_harian', 'amount' => 90000],
            ['item_name' => 'Beli beras 5 kg',       'category' => 'belanja_harian', 'amount' => 70000],
            ['item_name' => 'Beli minyak goreng 2L', 'category' => 'belanja_harian', 'amount' => 40000],
            ['item_name' => 'Beli teh & gula',       'category' => 'belanja_harian', 'amount' => 25000],
            ['item_name' => 'Gas LPG 3 kg',          'category' => 'operasional',    'amount' => 22000],
            ['item_name' => 'Beli jeruk 2 kg',       'category' => 'belanja_harian', 'amount' => 18000],
            ['item_name' => 'Plastik & styrofoam',   'category' => 'operasional',    'amount' => 15000],
            ['item_name' => 'Beli bumbu sambal',     'category' => 'belanja_harian', 'amount' => 30000],
            ['item_name' => 'Bayar listrik',         'category' => 'lainnya',        'amount' => 50000],
            ['item_name' => 'Beli kol & sayuran',    'category' => 'belanja_harian', 'amount' => 20000],
            ['item_name' => 'Beli tahu & tempe',     'category' => 'belanja_harian', 'amount' => 35000],
            ['item_name' => 'Air mineral isi ulang', 'category' => 'operasional',    'amount' => 12000],
        ];

        for ($hari = 13; $hari >= 0; $hari--) {
            $tanggal = Carbon::today()->subDays($hari);
            $isWeekend = $tanggal->isWeekend();
            $kasirHariIni = ($hari % 3 === 0) ? $admin : $kasir1;

            // Weekend lebih ramai
            $jumlahTransaksi = $isWeekend ? rand(12, 18) : rand(7, 12);

            for ($t = 0; $t < $jumlahTransaksi; $t++) {
                $maxJam = ($hari === 0) ? max(9, now()->hour) : 20;
                $waktuTransaksi = $tanggal->copy()
                    ->setHour(rand(9, $maxJam))
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
                    'invoice_number' => 'INV-'.$tanggal->format('ymd').'-'.str_pad($invoiceCounter, 4, '0', STR_PAD_LEFT),
                    'user_id' => $kasirHariIni->id,
                    'customer_name' => rand(0, 1) ? $this->namaAcak() : null,
                    'total_amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'cash_received' => $cashReceived,
                    'change' => $cashReceived - $totalAmount,
                    'status' => $status,
                    'created_at' => $waktuTransaksi,
                    'updated_at' => $waktuTransaksi,
                ]);

                // Order details
                foreach ($skenario as [$menuItem, $qty]) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'item_id' => $menuItem->id,
                        'quantity' => $qty,
                        'price' => $menuItem->price,
                        'subtotal' => $menuItem->price * $qty,
                        'notes' => null,
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
            $pKeys = array_rand($pengeluaranData, $jumlahP);
            if (! is_array($pKeys)) {
                $pKeys = [$pKeys];
            }

            foreach ($pKeys as $idx) {
                $p = $pengeluaranData[$idx];
                Pengeluaran::create([
                    'date' => $tanggal->format('Y-m-d'),
                    'item_name' => $p['item_name'],
                    'category' => $p['category'],
                    'amount' => max(5000, $p['amount'] + rand(-3000, 5000)),
                    'notes' => null,
                    'created_by' => $admin->id,
                    'created_at' => $tanggal->copy()->setHour(8)->setMinute(0),
                    'updated_at' => $tanggal->copy()->setHour(8)->setMinute(0),
                ]);
            }
        }

        // Bersihkan cache kategori & menu POS agar langsung tampil di layar kasir
        Cache::forget('pos_categories');
    }

    /**
     * Bulatkan ke nominal kembalian yang realistis.
     */
    private function roundUpToCash(int $total): int
    {
        foreach ([2000, 5000, 10000, 20000, 50000, 100000] as $p) {
            $result = (int) ceil($total / $p) * $p;
            if ($result >= $total) {
                return $result;
            }
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
