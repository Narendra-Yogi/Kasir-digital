<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        // Mengambil semua menu diurutkan dari yang terbaru beserta data kategorinya
        $items = Item::with('category')->latest()->get();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        // Mengambil seluruh data kategori untuk pilihan di form tambah menu
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Bersihkan tanda titik ribuan dari input harga sebelum divalidasi
        $request->merge([
            'price' => str_replace('.', '', $request->price),
        ]);

        // Validasi input form penambahan menu baru
        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'nullable|integer|min:0',
            'is_available' => 'required|boolean',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Batas ukuran 2MB
        ]);

        $jalurGambar = null;
        
        // Memproses upload file gambar jika diunggah pengguna
        if ($request->hasFile('image')) {
            $jalurGambar = $request->file('image')->store('items', 'public');
        }

        // Menyimpan menu baru ke database
        Item::create([
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'price'        => $request->price,
            'stock'        => $request->filled('stock') ? $request->stock : 0,
            'is_available' => $request->is_available,
            'image'        => $jalurGambar,
            'created_by'   => $request->user()->id,
        ]);

        Cache::forget('pos_categories'); // Invalidasi cache POS
        return redirect()->route('items.index')->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        // Bersihkan tanda titik ribuan dari input harga sebelum divalidasi
        $request->merge([
            'price' => str_replace('.', '', $request->price),
        ]);

        // Validasi input form edit/update menu
        $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:255',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'nullable|integer|min:0',
            'is_available' => 'required|boolean',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Batas ukuran 2MB
        ]);

        $jalurGambar = $item->image;

        // Memproses upload file gambar baru jika ada
        if ($request->hasFile('image')) {
            // Menghapus gambar lama dari penyimpanan publik jika sebelumnya ada gambar
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            // Menyimpan berkas gambar baru ke folder storage/app/public/items
            $jalurGambar = $request->file('image')->store('items', 'public');
        }

        // Memperbarui data menu di database
        $item->update([
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'price'        => $request->price,
            'stock'        => $request->filled('stock') ? $request->stock : 0,
            'is_available' => $request->is_available,
            'image'        => $jalurGambar,
            'updated_by'   => $request->user()->id,
        ]);

        Cache::forget('pos_categories'); // Invalidasi cache POS
        return redirect()->route('items.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        // Melakukan penghapusan secara Soft Delete
        $item->delete();
        Cache::forget('pos_categories'); // Invalidasi cache POS
        return redirect()->route('items.index')->with('success', 'Menu berhasil dihapus.');
    }
}