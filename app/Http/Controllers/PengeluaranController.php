<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    /**
     * Menampilkan daftar catatan pengeluaran dengan filter.
     */
    public function index(Request $request)
    {
        // Filter tanggal — default: awal bulan ini sampai akhir hari ini
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $pengeluaranQuery = Pengeluaran::with('user')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc');

        // Mengambil semua pengeluaran dalam rentang tanggal untuk kalkulasi ringkasan
        $semuaPengeluaran = (clone $pengeluaranQuery)->get();

        $totalExpense = $semuaPengeluaran->sum('amount');
        $totalBelanjaHarian = $semuaPengeluaran->where('category', 'belanja_harian')->sum('amount');
        $totalOperasional = $semuaPengeluaran->where('category', 'operasional')->sum('amount');
        $totalGaji = $semuaPengeluaran->where('category', 'gaji')->sum('amount');
        $totalLainnya = $semuaPengeluaran->where('category', 'lainnya')->sum('amount');
        // Alias lama agar view/PDF lama tidak error selama masa transisi
        $totalBahan = $totalBelanjaHarian;
        $totalLogistik = $totalOperasional;

        // Lakukan paginasi 20 data per halaman
        $expenses = $pengeluaranQuery->paginate(20)->appends($request->query());

        return view('pengeluaran.index', compact(
            'expenses', 'totalExpense', 'totalBelanjaHarian', 'totalOperasional', 'totalGaji', 'totalLainnya',
            'totalBahan', 'totalLogistik',
            'startDate', 'endDate'
        ));
    }

    /**
     * Menampilkan form untuk mencatat pengeluaran baru.
     */
    public function create()
    {
        return view('pengeluaran.create');
    }

    /**
     * Menyimpan data pengeluaran baru ke basis data.
     */
    public function store(Request $request)
    {
        // Bersihkan tanda titik ribuan dari nominal pengeluaran sebelum divalidasi
        $request->merge([
            'amount' => str_replace('.', '', $request->amount),
        ]);

        $request->validate([
            'date' => 'required|date',
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:belanja_harian,operasional,gaji,lainnya',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Pengeluaran::create([
            'date' => $request->date,
            'item_name' => $request->item_name,
            'category' => $request->category,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil dicatat.');
    }

    /**
     * Menampilkan form edit pengeluaran.
     */
    public function edit(Pengeluaran $pengeluaran)
    {
        return view('pengeluaran.edit', compact('pengeluaran'));
    }

    /**
     * Memperbarui data pengeluaran di basis data.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        // Bersihkan tanda titik ribuan dari nominal sebelum divalidasi
        $request->merge([
            'amount' => str_replace('.', '', $request->amount),
        ]);

        $request->validate([
            'date' => 'required|date',
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:belanja_harian,operasional,gaji,lainnya',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $pengeluaran->update([
            'date' => $request->date,
            'item_name' => $request->item_name,
            'category' => $request->category,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'updated_by' => $request->user()->id, // Catat admin yang mengedit data
        ]);

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Menghapus catatan pengeluaran.
     */
    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil dihapus.');
    }

    /**
     * Mengekspor data pengeluaran ke format berkas PDF.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $expenses = Pengeluaran::with('user')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc')->get();

        $totalExpense = $expenses->sum('amount');
        $totalBelanjaHarian = $expenses->where('category', 'belanja_harian')->sum('amount');
        $totalOperasional = $expenses->where('category', 'operasional')->sum('amount');
        $totalGaji = $expenses->where('category', 'gaji')->sum('amount');
        $totalLainnya = $expenses->where('category', 'lainnya')->sum('amount');
        $totalBahan = $totalBelanjaHarian;
        $totalLogistik = $totalOperasional;

        $pdf = Pdf::loadView('pdf.pengeluaran', compact(
            'expenses', 'totalExpense', 'totalBelanjaHarian', 'totalOperasional', 'totalGaji', 'totalLainnya',
            'totalBahan', 'totalLogistik', 'startDate', 'endDate'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Pengeluaran-'.$startDate->format('dMY').'-'.$endDate->format('dMY').'.pdf');
    }
}
