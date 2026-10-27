<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // ========================================================
    //  LAPORAN PENJUALAN / RINCIAN TRANSAKSI (INDEX)
    // ========================================================

    public function index(Request $request)
    {
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'month');

        $querySukses = Order::sukses()->antaraTanggal($tanggalMulai, $tanggalSelesai);

        $totalPendapatan = (clone $querySukses)->sum('total_amount');
        $totalTransaksi = (clone $querySukses)->count();
        $totalDibatalkan = Order::dibatalkan()->antaraTanggal($tanggalMulai, $tanggalSelesai)->count();
        $rataRataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        $rincianMetodeBayar = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('payment_method, COUNT(*) as jumlah, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->pluck('jumlah', 'payment_method');

        $jumlahTunai = $rincianMetodeBayar->get('cash', 0);
        $jumlahQris = $rincianMetodeBayar->get('qris', 0);

        $produkTerlaris = $this->ambilProdukTerlaris($tanggalMulai, $tanggalSelesai, 5);

        $pendapatanHarian = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labelGrafikMini = [];
        $dataGrafikMini = [];

        for ($d = $tanggalMulai->copy(); $d->lte($tanggalSelesai); $d->addDay()) {
            $formatTanggal = $d->format('Y-m-d');
            $labelGrafikMini[] = $d->format('d M');
            $dataGrafikMini[] = (int) ($pendapatanHarian->has($formatTanggal) ? $pendapatanHarian[$formatTanggal]->total : 0);
        }

        $pesanan = Order::with(['user', 'orderDetails.item' => function ($query) {
            $query->withTrashed();
        }])
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->latest('id')
            ->paginate(20)
            ->appends($request->query());

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
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Transaksi '.$order->invoice_number.' berhasil dibatalkan.');
    }

    // ========================================================
    //  REKAP HASIL USAHA (pengganti Rekap Laba Rugi)
    // ========================================================

    public function rekap(Request $request)
    {
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'week');

        $data = $this->ambilDataRekap($tanggalMulai, $tanggalSelesai);

        return view('reports.rekap', array_merge($data, compact('tanggalMulai', 'tanggalSelesai')));
    }

    // ========================================================
    //  LAPORAN HARIAN
    // ========================================================

    public function dailyReport(Request $request)
    {
        $tanggal = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $data = $this->ambilDataHarian($tanggal);

        return view('reports.daily', array_merge($data, compact('tanggal')));
    }

    // ========================================================
    //  EKSPOR PDF
    // ========================================================

    public function exportSalesPdf(Request $request)
    {
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'month');

        $pesanan = Order::with(['user', 'orderDetails.item' => function ($query) {
            $query->withTrashed();
        }])
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->latest('id')->get();

        $totalPendapatan = $pesanan->where('status', 'success')->sum('total_amount');
        $totalTransaksi = $pesanan->where('status', 'success')->count();
        $totalDibatalkan = $pesanan->where('status', 'cancelled')->count();
        $rataRataTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        $pdf = Pdf::loadView('pdf.sales', compact(
            'pesanan', 'totalPendapatan', 'totalTransaksi', 'totalDibatalkan', 'rataRataTransaksi', 'tanggalMulai', 'tanggalSelesai'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Penjualan-'.$tanggalMulai->format('dMY').'-'.$tanggalSelesai->format('dMY').'.pdf');
    }

    public function exportDailyPdf(Request $request)
    {
        $tanggal = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $data = $this->ambilDataHarian($tanggal);

        $pdf = Pdf::loadView('pdf.daily', array_merge($data, compact('tanggal')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Harian-'.$tanggal->format('d-M-Y').'.pdf');
    }

    public function exportRekapPdf(Request $request)
    {
        [$tanggalMulai, $tanggalSelesai] = $this->uraikanRentangTanggal($request, 'week');
        $data = $this->ambilDataRekap($tanggalMulai, $tanggalSelesai);

        $pdf = Pdf::loadView('pdf.rekap', array_merge($data, compact('tanggalMulai', 'tanggalSelesai')))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Rekap-Hasil-'.$tanggalMulai->format('dMY').'-'.$tanggalSelesai->format('dMY').'.pdf');
    }

    // ========================================================
    //  FUNGSI HELPER PRIVAT
    // ========================================================

    private function parseDateRange(Request $request, string $defaultRange = 'month'): array
    {
        return $this->uraikanRentangTanggal($request, $defaultRange);
    }

    private function uraikanRentangTanggal(Request $request, string $defaultRange = 'month'): array
    {
        $tanggalSelesai = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        if ($request->get('start_date')) {
            $tanggalMulai = Carbon::parse($request->get('start_date'))->startOfDay();
        } else {
            $tanggalMulai = match ($defaultRange) {
                'week' => Carbon::now()->subDays(6)->startOfDay(),
                'month' => Carbon::now()->startOfMonth(),
                default => Carbon::now()->startOfMonth(),
            };
        }

        return [$tanggalMulai, $tanggalSelesai];
    }

    private function getRekapData(Carbon $startDate, Carbon $endDate): array
    {
        return $this->ambilDataRekap($startDate, $endDate);
    }

    /**
     * Rekap Hasil Usaha:
     * Hasil Bersih = Pemasukan (order success) - Pengeluaran.
     */
    private function ambilDataRekap(Carbon $tanggalMulai, Carbon $tanggalSelesai): array
    {
        $pesanan = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_amount) as total_pemasukan, COUNT(*) as jumlah_transaksi')
            ->groupBy('tanggal')
            ->get()->keyBy('tanggal');

        $pengeluaran = Pengeluaran::whereBetween('date', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
            ->selectRaw('date, SUM(amount) as total_pengeluaran')
            ->groupBy('date')
            ->get()->keyBy('date');

        $pengeluaranPerKategori = Pengeluaran::whereBetween('date', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $metodeBayar = Order::sukses()
            ->antaraTanggal($tanggalMulai, $tanggalSelesai)
            ->selectRaw('payment_method, COUNT(*) as jumlah, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $rekapData = collect();
        $labelGrafik = [];
        $pemasukanGrafik = [];
        $pengeluaranGrafik = [];
        $hasilGrafik = [];

        $hariTerbaik = null;
        $hariTerburuk = null;
        $hariUntung = 0;

        for ($tanggalObjek = $tanggalMulai->copy(); $tanggalObjek->lte($tanggalSelesai); $tanggalObjek->addDay()) {
            $tanggalStr = $tanggalObjek->format('Y-m-d');
            $pemasukan = $pesanan->has($tanggalStr) ? (int) $pesanan[$tanggalStr]->total_pemasukan : 0;
            $jumlahTransaksi = $pesanan->has($tanggalStr) ? (int) $pesanan[$tanggalStr]->jumlah_transaksi : 0;
            $pengeluaranHariIni = $pengeluaran->has($tanggalStr) ? (int) $pengeluaran[$tanggalStr]->total_pengeluaran : 0;
            $hasil = $pemasukan - $pengeluaranHariIni;

            $labelGrafik[] = $tanggalObjek->format('d M');
            $pemasukanGrafik[] = $pemasukan;
            $pengeluaranGrafik[] = $pengeluaranHariIni;
            $hasilGrafik[] = $hasil;

            if ($pemasukan > 0 || $pengeluaranHariIni > 0) {
                $barisData = [
                    'tanggal' => $tanggalObjek->format('d M Y'),
                    'tanggal_iso' => $tanggalStr,
                    'pemasukan' => $pemasukan,
                    'pengeluaran' => $pengeluaranHariIni,
                    'total' => $hasil,
                    'transaksi' => $jumlahTransaksi,
                ];
                $rekapData->push($barisData);

                if ($hasil >= 0) {
                    $hariUntung++;
                }

                if ($hariTerbaik === null || $hasil > $hariTerbaik['total']) {
                    $hariTerbaik = $barisData;
                }
                if ($hariTerburuk === null || $hasil < $hariTerburuk['total']) {
                    $hariTerburuk = $barisData;
                }
            }
        }

        $totalPemasukan = $rekapData->sum('pemasukan');
        $totalPengeluaran = $rekapData->sum('pengeluaran');
        $totalTransaksi = $rekapData->sum('transaksi');
        $hasilBersih = $totalPemasukan - $totalPengeluaran;
        $marginBersih = $totalPemasukan > 0 ? round(($hasilBersih / $totalPemasukan) * 100, 1) : 0;
        $rataRataPemasukan = $rekapData->count() > 0 ? (int) round($totalPemasukan / $rekapData->count()) : 0;
        $rataRataTransaksi = $totalTransaksi > 0 ? (int) round($totalPemasukan / $totalTransaksi) : 0;

        // Perbandingan vs periode sebelumnya (durasi sama tepat sebelum tanggal mulai)
        $durasiHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;
        $prevSelesai = $tanggalMulai->copy()->subDay()->endOfDay();
        $prevMulai = $tanggalMulai->copy()->subDays($durasiHari)->startOfDay();
        $prevPemasukan = Order::sukses()->antaraTanggal($prevMulai, $prevSelesai)->sum('total_amount');
        $prevPengeluaran = Pengeluaran::whereBetween('date', [$prevMulai->format('Y-m-d'), $prevSelesai->format('Y-m-d')])->sum('amount');
        $prevHasil = $prevPemasukan - $prevPengeluaran;
        $selisihHasil = $hasilBersih - $prevHasil;

        // Menu terlaris & tersepi di periode ini
        $menuTerlaris = $this->ambilProdukTerlaris($tanggalMulai, $tanggalSelesai, 5);
        $menuTersepi = OrderDetail::whereHas('order', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->sukses()->antaraTanggal($tanggalMulai, $tanggalSelesai);
        })
            ->with(['item' => fn ($q) => $q->withTrashed()])
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('item_id')
            ->orderBy('total_qty')
            ->limit(3)
            ->get();

        $totalTunai = (int) ($metodeBayar->get('cash')->total ?? 0);
        $totalQris = (int) ($metodeBayar->get('qris')->total ?? 0);
        $jumlahTunai = (int) ($metodeBayar->get('cash')->jumlah ?? 0);
        $jumlahQris = (int) ($metodeBayar->get('qris')->jumlah ?? 0);

        // Peringatan stok produk menipis
        $stokMenipis = Item::where('stock', '<', 5)->orderBy('stock')->limit(5)->get();

        $result = [
            'rekapData' => $rekapData,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalTransaksi' => $totalTransaksi,
            'hasilBersih' => $hasilBersih,
            'profitMargin' => $marginBersih,
            'marginBersih' => $marginBersih,
            'rataRataPemasukan' => $rataRataPemasukan,
            'rataRataTransaksi' => $rataRataTransaksi,
            'hariUntung' => $hariUntung,
            'chartLabels' => $labelGrafik,
            'chartPemasukan' => $pemasukanGrafik,
            'chartPengeluaran' => $pengeluaranGrafik,
            'chartLaba' => $hasilGrafik,
            'chartHasil' => $hasilGrafik,
            'bestDay' => $hariTerbaik,
            'worstDay' => $hariTerburuk,
            'prevPemasukan' => $prevPemasukan,
            'prevPengeluaran' => $prevPengeluaran,
            'prevHasil' => $prevHasil,
            'selisihHasil' => $selisihHasil,
            'menuTerlaris' => $menuTerlaris,
            'produkTerlaris' => $menuTerlaris,
            'menuTersepi' => $menuTersepi,
            'totalTunai' => $totalTunai,
            'totalQris' => $totalQris,
            'jumlahTunai' => $jumlahTunai,
            'jumlahQris' => $jumlahQris,
            'pengeluaranPerKategori' => $pengeluaranPerKategori,
            'stokMenipis' => $stokMenipis,
        ];

        return $result;
    }

    private function getDailyData(Carbon $date): array
    {
        return $this->ambilDataHarian($date);
    }

    private function ambilDataHarian(Carbon $tanggal): array
    {
        $pesanan = Order::perTanggal($tanggal)->sukses()->get();
        $pengeluaran = Pengeluaran::whereDate('date', $tanggal)->get();

        $totalPenjualan = $pesanan->sum('total_amount');
        $totalPengeluaran = $pengeluaran->sum('amount');
        $hasilBersih = $totalPenjualan - $totalPengeluaran;

        $rincianBarang = OrderDetail::whereHas('order', function ($q) use ($tanggal) {
            $q->perTanggal($tanggal)->sukses();
        })
            ->with(['item' => fn ($q) => $q->withTrashed()])
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
            ->groupBy('item_id')
            ->orderByDesc('total_amount')
            ->get();

        $rincianKasir = Order::perTanggal($tanggal)->sukses()
            ->with('user')
            ->selectRaw('user_id, COUNT(*) as total_transactions, SUM(total_amount) as total_amount')
            ->groupBy('user_id')
            ->orderByDesc('total_amount')
            ->get();

        $pengeluaranPerKategori = $pengeluaran->groupBy('category')->map(fn ($rows) => $rows->sum('amount'));

        $stokMenipis = Item::where('stock', '<', 5)->orderBy('stock')->limit(5)->get();

        $kemarin = $tanggal->copy()->subDay();
        $penjualanKemarin = Order::perTanggal($kemarin)->sukses()->sum('total_amount');

        $pertumbuhanPenjualan = $penjualanKemarin > 0
            ? round((($totalPenjualan - $penjualanKemarin) / $penjualanKemarin) * 100, 1)
            : ($totalPenjualan > 0 ? 100 : 0);

        $result = [
            'pesanan' => $pesanan,
            'pengeluaran' => $pengeluaran,
            'pengeluaranPerKategori' => $pengeluaranPerKategori,
            'stokMenipis' => $stokMenipis,
            'totalPenjualan' => $totalPenjualan,
            'totalPengeluaran' => $totalPengeluaran,
            'hasilBersih' => $hasilBersih,
            'rincianBarang' => $rincianBarang,
            'rincianKasir' => $rincianKasir,
            'penjualanKemarin' => $penjualanKemarin,
            'pertumbuhanPenjualan' => $pertumbuhanPenjualan,
        ];

        return $result;
    }

    private function getTopSellingItems(Carbon $startDate, Carbon $endDate, int $limit = 5)
    {
        return $this->ambilProdukTerlaris($startDate, $endDate, $limit);
    }

    private function ambilProdukTerlaris(Carbon $tanggalMulai, Carbon $tanggalSelesai, int $limit = 5)
    {
        return OrderDetail::whereHas('order', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->sukses()->antaraTanggal($tanggalMulai, $tanggalSelesai);
        })
            ->with(['item' => fn ($q) => $q->withTrashed()])
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('item_id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();
    }
}
