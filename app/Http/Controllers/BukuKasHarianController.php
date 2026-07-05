<?php

namespace App\Http\Controllers;

use App\Models\BukuKasHarian;
use App\Models\Order;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class BukuKasHarianController extends Controller
{
    /**
     * Tampilan Log Buku Kas Harian
     */
    public function index()
    {
        $today = date('Y-m-d');
        
        // Cek apakah hari ini sudah melakukan tutup buku
        $alreadyClosed = BukuKasHarian::with('user')->where('date', $today)->first();

        // Mengambil semua riwayat tutup buku kasir diurutkan dari teranyar
        $historyLogs = BukuKasHarian::with('user')->latest('date')->get();

        // Variabel kalkulasi real-time sistem hari ini (jika belum ditutup)
        $systemCashSales = 0;
        $systemQrisSales = 0;
        $systemExpenses = 0;

        if (!$alreadyClosed) {
            // Jumlah Penjualan Tunai Sukses Hari Ini
            $systemCashSales = Order::whereDate('created_at', $today)
                ->where('status', 'success')
                ->where('payment_method', 'cash')
                ->sum('total_amount');

            // Jumlah Penjualan QRIS Sukses Hari Ini
            $systemQrisSales = Order::whereDate('created_at', $today)
                ->where('status', 'success')
                ->where('payment_method', 'qris')
                ->sum('total_amount');

            // Jumlah Pengeluaran Toko Hari Ini
            $systemExpenses = Pengeluaran::whereDate('date', $today)
                ->sum('amount');
        }

        return view('buku_kas_harian.index', compact(
            'alreadyClosed',
            'historyLogs',
            'systemCashSales',
            'systemQrisSales',
            'systemExpenses',
            'today'
        ));
    }

    /**
     * Menyimpan Tutup Buku Kasir Harian baru
     */
    public function store(Request $request)
    {
        $today = date('Y-m-d');

        // Validasi pembatasan ganda penutupan buku pada hari yang sama
        if (BukuKasHarian::where('date', $today)->exists()) {
            return back()->with('error', 'Laporan tutup buku kasir hari ini sudah dibuat sebelumnya.');
        }

        // Bersihkan tanda titik ribuan dari input modal & uang aktual sebelum divalidasi
        $request->merge([
            'starting_cash' => str_replace('.', '', $request->starting_cash),
            'actual_cash'   => str_replace('.', '', $request->actual_cash),
        ]);

        // Validasi input data modal awal & uang laci aktual
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
            'actual_cash'   => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:1000',
        ]);

        // Rekalkulasi data keuangan sistem untuk memvalidasi input di server
        $systemCashSales = Order::whereDate('created_at', $today)
            ->where('status', 'success')
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $systemQrisSales = Order::whereDate('created_at', $today)
            ->where('status', 'success')
            ->where('payment_method', 'qris')
            ->sum('total_amount');

        $systemExpenses = Pengeluaran::whereDate('date', $today)
            ->sum('amount');

        // Hitung estimasi kas teoritis sistem: (Modal Awal + Penjualan Tunai - Pengeluaran)
        $expectedCash = $request->starting_cash + $systemCashSales - $systemExpenses;

        // Hitung selisih kas: (Uang Fisik Aktual - Estimasi Uang Kas Sistem)
        $discrepancy = $request->actual_cash - $expectedCash;

        // Simpan data rekonsiliasi kas harian baru
        BukuKasHarian::create([
            'date'              => $today,
            'user_id'           => $request->user()->id,
            'starting_cash'     => $request->starting_cash,
            'system_cash_sales' => $systemCashSales,
            'system_qris_sales' => $systemQrisSales,
            'system_expenses'   => $systemExpenses,
            'actual_cash'       => $request->actual_cash,
            'discrepancy'       => $discrepancy,
            'notes'             => $request->notes,
        ]);

        return redirect()->route('buku-kas-harian.index')->with('success', 'Buku kas harian berhasil ditutup dan disimpan dengan aman.');
    }

    /**
     * Update data tutup buku kasir (hanya admin)
     * Hanya bisa mengubah actual_cash dan notes; discrepancy dihitung ulang.
     */
    public function update(Request $request, BukuKasHarian $bukuKasHarian)
    {
        // Bersihkan tanda titik ribuan dari input uang aktual
        $request->merge([
            'actual_cash' => str_replace('.', '', $request->actual_cash),
        ]);

        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:1000',
        ]);

        // Hitung ulang expected_cash dari data sistem yang sudah tersimpan
        $expectedCash = $bukuKasHarian->starting_cash
            + $bukuKasHarian->system_cash_sales
            - $bukuKasHarian->system_expenses;

        // Hitung ulang selisih berdasarkan uang aktual baru
        $discrepancy = $request->actual_cash - $expectedCash;

        $bukuKasHarian->update([
            'actual_cash'  => $request->actual_cash,
            'discrepancy'  => $discrepancy,
            'notes'        => $request->notes,
        ]);

        return redirect()->route('buku-kas-harian.index')->with('success', 'Data buku kas harian berhasil diperbarui.');
    }
}
