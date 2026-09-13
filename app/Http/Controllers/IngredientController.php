<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::latest()->get();

        // Summary data untuk card di atas tabel
        $totalBahan = $ingredients->count();
        $bahanHabis = $ingredients->where('current_stock', '<=', 0)->count();
        $bahanMenipis = $ingredients->filter(fn($i) => $i->stock_status === 'menipis')->count();

        return view('ingredients.index', compact('ingredients', 'totalBahan', 'bahanHabis', 'bahanMenipis'));
    }

    public function create()
    {
        return view('ingredients.create');
    }

    public function store(Request $request)
    {
        // Bersihkan tanda titik ribuan dari input harga sebelum divalidasi
        $request->merge([
            'purchase_price' => str_replace('.', '', $request->purchase_price),
        ]);

        $request->validate([
            'name'              => 'required|string|max:255',
            'purchase_price'    => 'required|numeric|min:1',
            'purchase_quantity' => 'required|numeric|min:0.01',
            'unit'              => 'required|string|max:20',
        ]);

        $ingredient = Ingredient::create([
            'name'              => $request->name,
            'purchase_price'    => $request->purchase_price,
            'purchase_quantity' => $request->purchase_quantity,
            'unit'              => $request->unit,
            'current_stock'     => $request->purchase_quantity, // Stok awal = jumlah beli
            'created_by'        => $request->user()->id,
        ]);

        // Otomatis catat pengeluaran kategori "bahan" agar user tidak perlu input 2 kali
        Pengeluaran::create([
            'date'          => now()->format('Y-m-d'),
            'item_name'     => 'Beli ' . $ingredient->name . ' (' . rtrim(rtrim(number_format($ingredient->purchase_quantity, 2, ',', '.'), '0'), ',') . ' ' . $ingredient->unit . ')',
            'category'      => 'bahan',
            'amount'        => $ingredient->purchase_price,
            'notes'         => 'Otomatis dari input bahan baku',
            'created_by'    => $request->user()->id,
            'ingredient_id' => $ingredient->id,
        ]);

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan & pengeluaran otomatis tercatat.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        // Bersihkan tanda titik ribuan dari input harga sebelum divalidasi
        $request->merge([
            'purchase_price' => str_replace('.', '', $request->purchase_price),
        ]);

        $request->validate([
            'name'              => 'required|string|max:255',
            'purchase_price'    => 'required|numeric|min:1',
            'purchase_quantity' => 'required|numeric|min:0.01',
            'unit'              => 'required|string|max:20',
        ]);

        $ingredient->update([
            'name'              => $request->name,
            'purchase_price'    => $request->purchase_price,
            'purchase_quantity' => $request->purchase_quantity,
            'unit'              => $request->unit,
        ]);

        // Recalculate HPP semua produk yang menggunakan bahan ini
        $this->recalculateAffectedItems($ingredient);

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil diperbarui & HPP produk terkait telah dihitung ulang.');
    }

    /**
     * Restok bahan baku yang sudah ada (beli tambahan).
     * Menambah current_stock, opsional update harga beli, dan catat pengeluaran.
     */
    public function restock(Request $request, Ingredient $ingredient)
    {
        $request->merge([
            'restock_price' => str_replace('.', '', $request->restock_price),
        ]);

        $request->validate([
            'restock_quantity'  => 'required|numeric|min:0.01',
            'restock_price'     => 'required|numeric|min:1',
        ]);

        $restockQty = $request->restock_quantity;
        $restockPrice = $request->restock_price;

        // Tambah stok
        $ingredient->addStock($restockQty);

        // Update harga beli & quantity referensi jika harga berubah
        $ingredient->update([
            'purchase_price'    => $restockPrice,
            'purchase_quantity' => $restockQty,
        ]);

        // Recalculate HPP produk terkait karena harga bahan mungkin berubah
        $this->recalculateAffectedItems($ingredient);

        // Catat pengeluaran restok
        Pengeluaran::create([
            'date'          => now()->format('Y-m-d'),
            'item_name'     => 'Restok ' . $ingredient->name . ' (' . rtrim(rtrim(number_format($restockQty, 2, ',', '.'), '0'), ',') . ' ' . $ingredient->unit . ')',
            'category'      => 'bahan',
            'amount'        => $restockPrice,
            'notes'         => 'Otomatis dari restok bahan baku',
            'created_by'    => $request->user()->id,
            'ingredient_id' => $ingredient->id,
        ]);

        return redirect()->route('ingredients.index')
            ->with('success', "Berhasil restok {$ingredient->name}: +" . rtrim(rtrim(number_format($restockQty, 2, ',', '.'), '0'), ',') . " {$ingredient->unit}. Pengeluaran otomatis tercatat.");
    }

    public function destroy(Ingredient $ingredient)
    {
        // Cek apakah bahan ini masih digunakan di produk
        $usedInItems = $ingredient->itemIngredients()->count();
        if ($usedInItems > 0) {
            return redirect()->route('ingredients.index')
                ->with('error', "Bahan \"{$ingredient->name}\" masih digunakan di {$usedInItems} produk. Hapus dari resep produk terlebih dahulu.");
        }

        // Hapus pengeluaran otomatis yang terkait dengan bahan ini
        Pengeluaran::where('ingredient_id', $ingredient->id)->delete();

        $ingredient->delete();
        return redirect()->route('ingredients.index')->with('success', 'Bahan baku & pengeluaran terkait berhasil dihapus.');
    }

    /**
     * API endpoint: Mengambil semua bahan baku (untuk AJAX di form produk).
     */
    public function apiList()
    {
        $ingredients = Ingredient::orderBy('name')->get(['id', 'name', 'cost_per_unit', 'unit', 'current_stock']);

        // Tambahkan data satuan kompatibel untuk setiap bahan
        $ingredients->each(function ($ingredient) {
            $ingredient->compatible_units = $ingredient->getCompatibleUnits();
        });

        return response()->json($ingredients);
    }

    /**
     * Menghitung ulang HPP semua produk yang menggunakan bahan ini.
     * Dipanggil saat harga bahan baku diupdate.
     */
    private function recalculateAffectedItems(Ingredient $ingredient): void
    {
        $items = $ingredient->items;
        foreach ($items as $item) {
            $item->recalculateHpp();
        }

        // Invalidasi cache POS karena HPP berubah
        Cache::forget('pos_categories');
    }
}

