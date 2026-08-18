<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'hpp',
        'stock',
        'is_available',
        'image',
        'created_by',
        'updated_by'
    ];

    /**
     * Menghitung margin keuntungan dalam persen.
     * Margin = (Harga Jual - HPP) / Harga Jual * 100
     */
    public function getMarginAttribute(): float
    {
        if ($this->price <= 0 || !$this->hpp) return 0;
        return round((($this->price - $this->hpp) / $this->price) * 100, 1);
    }

    /**
     * Menghitung laba kotor per unit.
     */
    public function getLabaPerUnitAttribute(): int
    {
        return max(0, $this->price - ($this->hpp ?? 0));
    }

    /**
     * Menghitung HPP otomatis dari total biaya semua bahan/resep.
     * HPP = Σ(ingredient.cost_per_unit × quantity_needed)
     */
    public function getHppFromIngredientsAttribute(): float
    {
        return $this->itemIngredients->sum(function ($itemIngredient) {
            return $itemIngredient->cost;
        });
    }

    /**
     * Menghitung ulang HPP dari resep bahan dan menyimpan ke kolom hpp.
     * Dipanggil setiap kali resep bahan diubah atau harga bahan diupdate.
     */
    public function recalculateHpp(): void
    {
        $this->load('itemIngredients.ingredient');
        $this->hpp = ceil($this->hpp_from_ingredients); // Pembulatan ke atas
        $this->saveQuietly(); // Simpan tanpa trigger events
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Detail resep bahan untuk produk ini.
     */
    public function itemIngredients(): HasMany
    {
        return $this->hasMany(ItemIngredient::class);
    }

    /**
     * Relasi: Bahan-bahan yang digunakan produk ini (many-to-many).
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'item_ingredients')
            ->withPivot('quantity_needed')
            ->withTimestamps();
    }
}
