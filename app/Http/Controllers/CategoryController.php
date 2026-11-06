<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        // Mengambil kategori dengan hitungan jumlah item di dalamnya
        $categories = Kategori::withCount('items')->latest()->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:kategori,name',
        ]);

        Kategori::create([
            'name' => $request->name,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Kategori $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:kategori,name,'.$category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'updated_by' => $request->user()->id,
        ]);

        Cache::forget('pos_categories');

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $category)
    {
        // Cek apakah kategori masih memiliki item
        if ($category->items()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki menu di dalamnya.');
        }

        $category->delete();

        Cache::forget('pos_categories');

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
