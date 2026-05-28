<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filter tanggal — default: hari ini
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $inventories = Inventory::with('user')
            ->whereDate('date', $date)
            ->orderBy('item_name', 'asc')
            ->get();

        // Summary data
        $totalItems = $inventories->count();
        $criticalStock = $inventories->where('remaining_stock', '<', 5)->count();
        $outOfStock = $inventories->where('remaining_stock', '<=', 0)->count();

        return view('inventories.index', compact('inventories', 'date', 'totalItems', 'criticalStock', 'outOfStock'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'item_name' => 'required|string|max:255',
            'new_stock' => 'required|numeric|min:0',
            'old_stock' => 'required|numeric|min:0',
            'sold' => 'required|numeric|min:0',
        ]);

        Inventory::create([
            'date' => $request->date,
            'item_name' => $request->item_name,
            'new_stock' => $request->new_stock,
            'old_stock' => $request->old_stock,
            'sold' => $request->sold,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('inventories.index')->with('success', 'Data stok berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        return view('inventories.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'date' => 'required|date',
            'item_name' => 'required|string|max:255',
            'new_stock' => 'required|numeric|min:0',
            'old_stock' => 'required|numeric|min:0',
            'sold' => 'required|numeric|min:0',
        ]);

        $inventory->update([
            'date' => $request->date,
            'item_name' => $request->item_name,
            'new_stock' => $request->new_stock,
            'old_stock' => $request->old_stock,
            'sold' => $request->sold,
        ]);

        return redirect()->route('inventories.index')->with('success', 'Data stok berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success', 'Data stok berhasil dihapus.');  
    }

    /**
     * Export inventory to PDF.
     */
    public function exportPdf(Request $request)
    {
        $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $inventories = Inventory::with('user')
            ->whereDate('date', $date)
            ->orderBy('item_name', 'asc')
            ->get();

        $totalItems = $inventories->count();
        $criticalStock = $inventories->where('remaining_stock', '<', 5)->count();
        $outOfStock = $inventories->where('remaining_stock', '<=', 0)->count();

        $pdf = Pdf::loadView('pdf.inventories', compact('inventories', 'date', 'totalItems', 'criticalStock', 'outOfStock'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Stok-Logistik-' . $date->format('d-M-Y') . '.pdf');
    }
}
