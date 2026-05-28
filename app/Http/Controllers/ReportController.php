<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // ========================================================
    //  LAPORAN PENJUALAN (INDEX)
    // ========================================================

    public function index(Request $request)
    {
        // Mendapatkan rentang tanggal mulai dan selesai dari request (default: bulan ini)
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'month');

        // --- Menggunakan query aggregate (efisien, tidak meload semua baris data ke memory RAM) ---
        // Membuat basis query untuk transaksi yang sukses di antara rentang tanggal yang dipilih
        $querySukses = Order::sukses()->antaraTanggal($tanggalMulai, $tanggalSelesai);

        // Menghitung total omzet pendapatan dari transaksi yang sukses
        $totalPendapatan  = (clone $querySukses)->sum('total_amount');
        // Menghitung total jumlah transaksi sukses
        $totalTransaksi   = (clone $querySukses)->count();
        // Menghitung jumlah transaksi yang dibatalkan
        $totalDibatalkan  = Order::dibatalkan()->antaraTanggal($tanggalMulai, $tanggalSelesai)->count();
        // Menghitung rata-rata nilai transaksi
        $rataRataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // --- Breakdown Metode Pembayaran ---
        // Menggunakan 1 query dengan GROUP BY untuk efisiensi performa database
        $rincianMetodeBayar = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('payment_method, COUNT(*) as jumlah, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('jumlah', 'payment_method');

        // Mengambil jumlah masing-masing metode pembayaran (tunai / qris)
        $jumlahTunai = $rincianMetodeBayar->get('cash', 0);
        $jumlahQris  = $rincianMetodeBayar->get('qris', 0);

        // --- Mendapatkan daftar 5 produk terlaris dalam periode ini ---
        $produkTerlaris = $this->ambilProdukTerlaris($tanggalMulai, $tanggalSelesai, 5);

        // --- Data Grafik Mini: Tren Pendapatan Harian ---
        $pendapatanHarian = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labelGrafikMini = [];
        $dataGrafikMini  = [];
        
        // Melakukan looping dari tanggal mulai sampai tanggal selesai untuk mengisi data grafik
        for ($d = $tanggalMulai->copy(); $d->lte($tanggalSelesai); $d->addDay()) {
            $formatTanggal = $d->format('Y-m-d');
            $labelGrafikMini[] = $d->format('d M');
            $dataGrafikMini[]  = (int) ($pendapatanHarian->has($formatTanggal) ? $pendapatanHarian[$formatTanggal]->total : 0);
        }

        // --- Mengambil data transaksi dengan pagination (20 data per halaman) ---
        $pesanan = Order::with(['user', 'orderDetails.item' => function($query) {
                $query->withTrashed(); // Tetap menampilkan produk meskipun sudah dihapus soft-delete
            }])
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->latest()
            ->paginate(20)
            ->appends($request->query()); // Menjaga agar filter filter tanggal tetap terbawa saat ganti halaman

        // Mengirimkan data yang siap pakai ke view laporan utama
        return view('reports.index', compact(
            'pesanan', 'totalPendapatan', 'totalTransaksi', 'totalDibatalkan', 
            'rataRataTransaksi', 'jumlahTunai', 'jumlahQris', 'tanggalMulai', 'tanggalSelesai',
            'produkTerlaris', 'labelGrafikMini', 'dataGrafikMini'
        ));
    }

    // ========================================================
    //  BATALKAN TRANSAKSI
    // ========================================================

    public function cancel(Order $order)
    {
        // Memeriksa jika transaksi tersebut memang sudah dibatalkan sebelumnya
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        // Mengubah status transaksi menjadi dibatalkan
        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Transaksi ' . $order->invoice_number . ' berhasil dibatalkan.');
    }

    // ========================================================
    //  REKAP LABA RUGI
    // ========================================================

    public function rekap(Request $request)
    {
        // Menguraikan rentang tanggal (default: 7 hari terakhir / seminggu)
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'week');
        
        // Memanggil fungsi helper privat untuk mengambil rekap data laba rugi
        $data = $this->ambilDataRekap($tanggalMulai, $tanggalSelesai);

        return view('reports.rekap', array_merge($data, compact('tanggalMulai', 'tanggalSelesai')));
    }

    // ========================================================
    //  LAPORAN HARIAN
    // ========================================================

    public function dailyReport(Request $request)
    {
        // Mendapatkan tanggal yang diinput atau menggunakan tanggal hari ini sebagai default
        $tanggal = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        // Memanggil fungsi privat untuk memproses laporan harian
        $data = $this->ambilDataHarian($tanggal);

        return view('reports.daily', array_merge($data, compact('tanggal')));
    }

    // ========================================================
    //  EKSPOR PDF
    // ========================================================

    public function exportSalesPdf(Request $request)
    {
        // Ekspor PDF Laporan Penjualan bulanan/rentang tertentu
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'month');

        $pesanan = Order::with(['user', 'orderDetails.item' => function($query) {
                $query->withTrashed();
            }])
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->latest()->get();

        $totalPendapatan  = $pesanan->where('status', 'success')->sum('total_amount');
        $totalTransaksi   = $pesanan->where('status', 'success')->count();
        $totalDibatalkan  = $pesanan->where('status', 'cancelled')->count();
        $rataRataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        $pdf = Pdf::loadView('pdf.sales', compact(
            'pesanan', 'totalPendapatan', 'totalTransaksi', 'totalDibatalkan', 'rataRataTransaksi', 'tanggalMulai', 'tanggalSelesai'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Penjualan-' . $tanggalMulai->format('dMY') . '-' . $tanggalSelesai->format('dMY') . '.pdf');
    }

    public function exportDailyPdf(Request $request)
    {
        // Ekspor PDF Laporan Harian
        $tanggal = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $data = $this->ambilDataHarian($tanggal);

        $pdf = Pdf::loadView('pdf.daily', array_merge($data, compact('tanggal')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Harian-' . $tanggal->format('d-M-Y') . '.pdf');
    }

    public function exportRekapPdf(Request $request)
    {
        // Ekspor PDF Rekapitulasi Laba Rugi mingguan/rentang tertentu
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'week');
        $data = $this->ambilDataRekap($tanggalMulai, $tanggalSelesai);

        $pdf = Pdf::loadView('pdf.rekap', array_merge($data, compact('tanggalMulai', 'tanggalSelesai')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Rekap-LabaRugi-' . $tanggalMulai->format('dMY') . '-' . $tanggalSelesai->format('dMY') . '.pdf');
    }

    // ========================================================
    //  FUNGSI HELPER PRIVAT (INTERNAL LOGIC)
    // ========================================================

    /**
     * Memproses rentang tanggal mulai dan selesai dari Request secara pintar.
     */
    private function parseDateRange(Request $request, string $defaultRange = 'month'): array
    {
        return $this->uraikanRentangTanggal($request, $defaultRange);
    }

    private function uraikanRentangTanggal(Request $request, string $defaultRange = 'month'): array
    {
        // Mengambil tanggal selesai (jika kosong default: akhir hari ini)
        $tanggalSelesai = $request->get('end_date') 
            ? Carbon::parse($request->get('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

        // Mengambil tanggal mulai
        if ($request->get('start_date')) {
            $tanggalMulai = Carbon::parse($request->get('start_date'))->startOfDay();
        } else {
            $tanggalMulai = match($defaultRange) {
                'week' => Carbon::now()->subDays(6)->startOfDay(),
                'month' => Carbon::now()->startOfMonth(),
                default => Carbon::now()->startOfMonth(),
            };
        }

        return [$tanggalMulai, $tanggalSelesai];
    }

    /**
     * Mengambil data rekapitulasi laba rugi yang digunakan di View dan Ekspor PDF.
     */
    private function getRekapData(Carbon $startDate, Carbon $endDate): array
    {
        return $this->ambilDataRekap($startDate, $endDate);
    }

    private function ambilDataRekap(Carbon $tanggalMulai, Carbon $tanggalSelesai): array
    {
        // Mengambil pemasukan harian (transaksi sukses) yang di-group per tanggal
        $pesanan = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total_pemasukan')
            ->groupBy('tanggal')
            ->get()->keyBy('tanggal');

        // Mengambil pengeluaran harian yang di-group per tanggal
        $pengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
            ->selectRaw('date, SUM(amount) as total_pengeluaran')
            ->groupBy('date')
            ->get()->keyBy('date');

        $rekapData = collect();
        $labelGrafik = [];
        $pemasukanGrafik = [];
        $pengeluaranGrafik = [];
        $labaGrafik = [];

        $hariTerbaik = null;
        $hariTerburuk = null;

        // Loop harian untuk menghitung laba bersih per hari
        for ($tanggalObjek = $tanggalMulai->copy(); $tanggalObjek->lte($tanggalSelesai); $tanggalObjek->addDay()) {
            $tanggalStr = $tanggalObjek->format('Y-m-d');
            $pemasukan = $pesanan->has($tanggalStr) ? $pesanan[$tanggalStr]->total_pemasukan : 0;
            $pengeluaranHariIni = $pengeluaran->has($tanggalStr) ? $pengeluaran[$tanggalStr]->total_pengeluaran : 0;
            $laba = $pemasukan - $pengeluaranHariIni;
            
            $labelGrafik[] = $tanggalObjek->format('d M');
            $pemasukanGrafik[] = (int) $pemasukan;
            $pengeluaranGrafik[] = (int) $pengeluaranHariIni;
            $labaGrafik[] = (int) $laba;

            // Hanya menyimpan baris jika ada aktivitas pemasukan atau pengeluaran
            if ($pemasukan > 0 || $pengeluaranHariIni > 0) {
                $barisData = [
                    'tanggal' => $tanggalObjek->format('d M Y'),
                    'pemasukan' => $pemasukan,
                    'pengeluaran' => $pengeluaranHariIni,
                    'total' => $laba,
                ];
                $rekapData->push($barisData);

                // Mencari hari terbaik (laba tertinggi) dan terburuk (laba terendah)
                if ($hariTerbaik === null || $laba > $hariTerbaik['total']) $hariTerbaik = $barisData;
                if ($hariTerburuk === null || $laba < $hariTerburuk['total']) $hariTerburuk = $barisData;
            }
        }

        $totalPemasukan = $rekapData->sum('pemasukan');
        $totalPengeluaran = $rekapData->sum('pengeluaran');
        $labaBersih = $totalPemasukan - $totalPengeluaran;
        // Menghitung margin laba kotor/bersih dalam persen
        $marginKeuntungan = $totalPemasukan > 0 ? round(($labaBersih / $totalPemasukan) * 100, 1) : 0;

        return [
            'rekapData' => $rekapData,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'labaBersih' => $labaBersih,
            'profitMargin' => $marginKeuntungan, // Tetap gunakan key 'profitMargin' agar aman di rekap.blade.php lama
            'chartLabels' => $labelGrafik,
            'chartPemasukan' => $pemasukanGrafik,
            'chartPengeluaran' => $pengeluaranGrafik,
            'chartLaba' => $labaGrafik,
            'bestDay' => $hariTerbaik,
            'worstDay' => $hariTerburuk
        ];
    }

    /**
     * Mengambil data laporan harian lengkap yang digunakan di View dan Ekspor PDF.
     */
    private function getDailyData(Carbon $date): array
    {
        return $this->ambilDataHarian($date);
    }

    private function ambilDataHarian(Carbon $tanggal): array
    {
        $pesanan = Order::perTanggal($tanggal)->sukses()->get();
        $stokBarang = Inventory::whereDate('date', $tanggal)->get();
        $pengeluaran = Pengeluaran::whereDate('date', $tanggal)->get();

        $totalPenjualan = $pesanan->sum('total_amount');
        $totalPengeluaran = $pengeluaran->sum('amount');
        $labaBersih = $totalPenjualan - $totalPengeluaran;

        // Rincian penjualan per item/menu hari ini
        $rincianBarang = OrderDetail::whereHas('order', function($q) use ($tanggal) {
                $q->perTanggal($tanggal)->sukses();
            })
            ->with(['item' => fn($q) => $q->withTrashed()])
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
            ->groupBy('item_id')
            ->orderByDesc('total_amount')
            ->get();

        // Rincian penjualan per kasir yang melayani
        $rincianKasir = Order::perTanggal($tanggal)->sukses()
            ->with('user')
            ->selectRaw('user_id, COUNT(*) as total_transactions, SUM(total_amount) as total_amount')
            ->groupBy('user_id')
            ->orderByDesc('total_amount')
            ->get();

        // Mengambil data perbandingan penjualan dengan hari kemarin
        $kemarin = $tanggal->copy()->subDay();
        $penjualanKemarin = Order::perTanggal($kemarin)->sukses()->sum('total_amount');

        $pertumbuhanPenjualan = $penjualanKemarin > 0 
            ? round((($totalPenjualan - $penjualanKemarin) / $penjualanKemarin) * 100, 1) 
            : ($totalPenjualan > 0 ? 100 : 0);

        return [
            'pesanan' => $pesanan,
            'stokBarang' => $stokBarang,
            'pengeluaran' => $pengeluaran,
            'totalPenjualan' => $totalPenjualan,
            'totalPengeluaran' => $totalPengeluaran,
            'labaBersih' => $labaBersih,
            'rincianBarang' => $rincianBarang,
            'rincianKasir' => $rincianKasir,
            'penjualanKemarin' => $penjualanKemarin,
            'pertumbuhanPenjualan' => $pertumbuhanPenjualan
        ];
    }
    /**
     * Mengambil item dengan total kuantitas penjualan tertinggi dalam rentang tanggal.
     */
    private function getTopSellingItems(Carbon $startDate, Carbon $endDate, int $limit = 5)
    {
        return $this->ambilProdukTerlaris($startDate, $endDate, $limit);
    }

    private function ambilProdukTerlaris(Carbon $tanggalMulai, Carbon $tanggalSelesai, int $limit = 5)
    {
        return OrderDetail::whereHas('order', function($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->sukses()->antaraTanggal($tanggalMulai, $tanggalSelesai);
            })
            ->with(['item' => fn($q) => $q->withTrashed()])
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('item_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }
}