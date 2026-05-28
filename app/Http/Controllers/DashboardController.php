<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Mendapatkan tanggal hari ini dan kemarin menggunakan library Carbon
        $hariIni = Carbon::today();
        $kemarin = Carbon::yesterday();
        
        // --- 1. Statistik Hari Ini (Menggunakan scope query bahasa Indonesia dari Model Order) ---
        // Menghitung jumlah transaksi sukses hari ini
        $totalPesananHariIni     = Order::perTanggal($hariIni)->sukses()->count();
        // Menghitung total omzet (jumlah pendapatan) dari transaksi sukses hari ini
        $totalPendapatanHariIni  = Order::perTanggal($hariIni)->sukses()->sum('total_amount');
        // Menghitung transaksi yang dibatalkan hari ini
        $pesananDibatalkanHariIni = Order::perTanggal($hariIni)->dibatalkan()->count();
        // Menghitung total pengeluaran hari ini dari tabel pengeluaran
        $totalPengeluaranHariIni  = Pengeluaran::whereDate('date', $hariIni)->sum('amount');

        // --- 2. Perbandingan dengan Hari Kemarin untuk Indikator Pertumbuhan ---
        // Menghitung omzet kemarin untuk dibandingkan dengan hari ini
        $pendapatanKemarin = Order::perTanggal($kemarin)->sukses()->sum('total_amount');
        
        // Rumus pertumbuhan: ((Hari ini - Kemarin) / Kemarin) * 100
        $pertumbuhanPendapatan = $pendapatanKemarin > 0 
            ? round((($totalPendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100, 1) 
            : ($totalPendapatanHariIni > 0 ? 100 : 0);

        // --- 3. Analitik Penjualan Mingguan: Menggunakan 1 query aggregate untuk efisiensi ---
        $awalMinggu = Carbon::today()->subDays(6);
        $penjualanMingguanMentah = Order::sukses()
            ->whereBetween('created_at', [$awalMinggu->startOfDay(), Carbon::now()->endOfDay()])
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        // Menyiapkan label hari dalam Bahasa Indonesia untuk chart/grafik
        $namaHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $penjualanMingguan = [];
        $hari = [];
        
        // Looping untuk menyusun data 7 hari terakhir agar urut dari 6 hari lalu sampai hari ini
        for ($i = 6; $i >= 0; $i--) {
            $tanggalObjek = Carbon::today()->subDays($i);
            // Mengambil nama hari singkat berdasarkan hari dalam seminggu (0 untuk Minggu, 6 untuk Sabtu)
            $hari[] = $namaHari[$tanggalObjek->dayOfWeek];
            // Mengambil total penjualan pada tanggal tersebut (jika tidak ada transaksi, default 0)
            $penjualanMingguan[] = (int) ($penjualanMingguanMentah->get($tanggalObjek->format('Y-m-d'), 0));
        }

        // --- 4. Produk Terlaris Hari Ini (Top 5) ---
        $produkTerlarisHariIni = OrderDetail::whereHas('order', function($q) use ($hariIni) {
                $q->perTanggal($hariIni)->sukses();
            })
            ->with(['item' => fn($q) => $q->withTrashed()])
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('item_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // --- 5. Daftar Transaksi Terbaru (5 transaksi terakhir) ---
        $transaksiTerbaru = Order::with('user')->latest()->take(5)->get();

        // Mengirimkan semua data yang sudah diproses ke halaman view dashboard
        return view('dashboard', compact(
            'totalPesananHariIni', 
            'totalPendapatanHariIni', 
            'pesananDibatalkanHariIni', 
            'totalPengeluaranHariIni',
            'pendapatanKemarin',
            'pertumbuhanPendapatan',
            'hari', 
            'penjualanMingguan', 
            'produkTerlarisHariIni',
            'transaksiTerbaru'
        ));
    }
}