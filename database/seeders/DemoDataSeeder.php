<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pengeluaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Seeder data demo Kasir Digital "Geprek Legend".
 *
 * MENGHAPUS seluruh data transaksi (order, pengeluaran, buku kas) lalu
 * mengisi ulang 7 hari terakhir dengan data yang mudah dibaca.
 * Data master (users, kategori, menu) TIDAK disentuh.
 *
 * Bisa dijalankan ulang kapan saja:
 *   php artisan db:seed --class=DemoDataSeeder --force
 */
class DemoDataSeeder extends Seeder
{
    /**
     * Daftar stok awal per nama menu (angka bulat, mudah dibaca).
     *
     * @var array<string, int>
     */
    private const STOK_AWAL = [
        'Paket Geprek Dada + Nasi' => 50,
        'Paket Geprek Paha + Nasi' => 50,
        'Paket Geprek 2 Paha + Nasi' => 30,
        'Ayam Geprek Dada (Tanpa Nasi)' => 40,
        'Ayam Geprek Paha (Tanpa Nasi)' => 40,
        'Nasi Putih' => 100,
        'Es Teh Manis' => 200,
        'Es Jeruk' => 200,
        'Air Mineral' => 200,
        'Es Teh Tawar' => 200,
        'Kol Goreng' => 150,
        'Tahu / Tempe' => 150,
        'Telur Ceplok' => 60,
    ];

    /**
     * Jadwal transaksi demo 7 hari terakhir.
     * Format: [hari lalu, jam, menit, pelanggan, baris item, metode, uang diterima, status]
     * Uang diterima cash ditulis manual agar angkanya realistis; QRIS otomatis pas.
     *
     * @var array<int, array{0: int, 1: int, 2: int, 3: ?string, 4: array<int, array{0: string, 1: int}>, 5: string, 6: int, 7: string}>
     */
    private const JADWAL = [
        // --- 6 hari lalu ---
        [6, 11, 5, null, [['Nasi Putih', 2], ['Es Teh Manis', 2]], 'cash', 20000, 'success'],
        [6, 12, 30, 'Bu Sari', [['Paket Geprek Dada + Nasi', 3], ['Telur Ceplok', 1]], 'qris', 65000, 'success'],
        [6, 18, 15, 'Pak Wawan', [['Ayam Geprek Dada (Tanpa Nasi)', 2], ['Es Jeruk', 1]], 'cash', 40000, 'success'],

        // --- 5 hari lalu ---
        [5, 11, 40, null, [['Paket Geprek Paha + Nasi', 2]], 'cash', 40000, 'success'],
        [5, 13, 10, 'Mas Dimas', [['Paket Geprek 2 Paha + Nasi', 4], ['Kol Goreng', 2]], 'qris', 106000, 'success'],
        [5, 17, 45, 'Sinta', [['Tahu / Tempe', 3], ['Air Mineral', 2]], 'cash', 20000, 'success'],

        // --- 4 hari lalu ---
        [4, 11, 20, null, [['Paket Geprek Dada + Nasi', 2], ['Es Teh Manis', 2]], 'qris', 48000, 'success'],
        [4, 12, 50, 'Fajar', [['Ayam Geprek Paha (Tanpa Nasi)', 3], ['Nasi Putih', 2]], 'cash', 60000, 'success'],

        // --- 3 hari lalu ---
        [3, 11, 10, 'Bu Warni', [['Paket Geprek 2 Paha + Nasi', 3], ['Telur Ceplok', 2]], 'cash', 90000, 'success'],
        [3, 18, 20, null, [['Nasi Putih', 1], ['Es Teh Manis', 1]], 'qris', 9000, 'success'],

        // --- 2 hari lalu ---
        [2, 11, 30, 'Deni', [['Paket Geprek Dada + Nasi', 4]], 'qris', 80000, 'success'],
        [2, 13, 0, null, [['Ayam Geprek Dada (Tanpa Nasi)', 2], ['Kol Goreng', 1]], 'cash', 40000, 'success'],
        [2, 19, 0, 'Pak RT', [['Paket Geprek Paha + Nasi', 2], ['Es Jeruk', 2]], 'cash', 50000, 'success'],

        // --- 1 hari lalu (kemarin) ---
        [1, 11, 15, 'Rina', [['Paket Geprek 2 Paha + Nasi', 2], ['Es Teh Manis', 2]], 'cash', 60000, 'success'],
        [1, 12, 40, null, [['Paket Geprek Dada + Nasi', 3], ['Tahu / Tempe', 2]], 'qris', 64000, 'success'],
        [1, 18, 10, 'Ayu', [['Ayam Geprek Paha (Tanpa Nasi)', 2], ['Nasi Putih', 1]], 'cash', 40000, 'success'],
        [1, 15, 0, 'Deni', [['Paket Geprek Dada + Nasi', 2]], 'cash', 40000, 'cancelled'], // contoh transaksi batal

        // --- Hari ini ---
        [0, 11, 5, null, [['Paket Geprek Dada + Nasi', 2], ['Es Teh Manis', 2]], 'cash', 50000, 'success'],
        [0, 12, 20, 'Sinta', [['Ayam Geprek Paha (Tanpa Nasi)', 2], ['Nasi Putih', 1]], 'qris', 33000, 'success'],
    ];

    /**
     * Pengeluaran demo 7 hari terakhir.
     * Format: [hari lalu, nama pengeluaran, kategori, nominal]
     *
     * @var array<int, array{0: int, 1: string, 2: string, 3: int}>
     */
    private const PENGELUARAN = [
        [6, 'Ayam dada segar 5 kg', 'belanja_harian', 90000],
        [5, 'Minyak goreng 2 L', 'belanja_harian', 70000],
        [4, 'Gas LPG 3 kg', 'operasional', 35000],
        [4, 'Bawang & cabai', 'belanja_harian', 45000],
        [3, 'Sedotan & kantong plastik', 'operasional', 25000],
        [2, 'Tahu & tempe', 'belanja_harian', 30000],
        [1, 'Upah dapur harian', 'gaji', 80000],
        [1, 'Beras 5 kg', 'belanja_harian', 50000],
        [0, 'Tisu & plastik', 'operasional', 15000],
    ];

    public function run(): void
    {
        // ============================================================
        // 1. BERSIHKAN SELURUH DATA TRANSAKSI (menu & user tetap aman)
        // ============================================================
        DB::table('detail_pesanan')->delete();
        DB::table('pesanan')->delete();
        DB::table('pengeluaran')->delete();
        DB::table('buku_kas_harian')->delete();

        $admin = User::where('role', 'admin')->orderBy('id')->first();
        $kasir = User::where('role', 'kasir')->orderBy('id')->first() ?? $admin;
        $menu = Item::all()->keyBy('name');

        // ============================================================
        // 2. TRANSAKSI SESUAI JADWAL
        // ============================================================
        $terjual = []; // [item_id => total qty sukses]
        $invoiceCounter = 1;

        foreach (self::JADWAL as [$hariLalu, $jam, $menit, $pelanggan, $barisItem, $metode, $diterima, $status]) {
            $waktu = Carbon::today()->subDays($hariLalu)->setTime($jam, $menit, 0);
            $total = 0;

            foreach ($barisItem as [$namaItem, $qty]) {
                $total += $menu[$namaItem]->price * $qty;
            }

            $invoice = 'INV-'.$waktu->format('ymd').'-'.str_pad((string) $invoiceCounter, 4, '0', STR_PAD_LEFT);
            $invoiceCounter++;

            // Instansiasi langsung agar created_at/updated_at historis terisi
            // (kolom timestamp tidak berada di $fillable Order).
            $order = new Order;
            $order->invoice_number = $invoice;
            $order->user_id = ($invoiceCounter % 2 === 0 ? $kasir : $admin)->id;
            $order->customer_name = $pelanggan;
            $order->total_amount = $total;
            $order->payment_method = $metode;
            $order->cash_received = $metode === 'qris' ? $total : $diterima;
            $order->change = $metode === 'qris' ? 0 : ($diterima - $total);
            $order->status = $status;
            $order->created_at = $waktu;
            $order->updated_at = $waktu;
            $order->save();

            $detailRows = [];

            foreach ($barisItem as [$namaItem, $qty]) {
                $item = $menu[$namaItem];

                $detailRows[] = [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'price' => $item->price,
                    'subtotal' => $item->price * $qty,
                    'notes' => null,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ];

                // Stok hanya berkurang untuk transaksi yang sukses.
                if ($status === 'success') {
                    $terjual[$item->id] = ($terjual[$item->id] ?? 0) + $qty;
                }
            }

            OrderDetail::insert($detailRows);
        }

        // ============================================================
        // 3. PENGELUARAN SESUAI DAFTAR
        // ============================================================
        foreach (self::PENGELUARAN as [$hariLalu, $nama, $kategori, $nominal]) {
            $waktu = Carbon::today()->subDays($hariLalu)->setTime(8, 30, 0);

            $pengeluaran = new Pengeluaran;
            $pengeluaran->date = $waktu->toDateString();
            $pengeluaran->item_name = $nama;
            $pengeluaran->category = $kategori;
            $pengeluaran->amount = $nominal;
            $pengeluaran->notes = null;
            $pengeluaran->created_by = $admin->id;
            $pengeluaran->created_at = $waktu;
            $pengeluaran->updated_at = $waktu;
            $pengeluaran->save();
        }

        // ============================================================
        // 4. SET STOK AKHIR = STOK AWAL − TERJUAL (transaksi sukses)
        // ============================================================
        foreach (self::STOK_AWAL as $namaItem => $stokAwal) {
            $item = $menu[$namaItem];
            $item->stock = $stokAwal - ($terjual[$item->id] ?? 0);
            $item->save();
        }

        // Segarkan tampilan kasir agar data baru langsung terbaca
        Cache::forget('pos_categories');
    }
}
