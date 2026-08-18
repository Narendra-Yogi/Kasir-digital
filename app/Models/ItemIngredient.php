<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemIngredient extends Model
{
    protected $fillable = [
        'item_id',
        'ingredient_id',
        'quantity_needed',
        'unit_used',
    ];

    /**
     * Menghitung biaya bahan ini untuk 1 porsi produk, dengan konversi satuan.
     *
     * Jika unit_used berbeda dari ingredient.unit, konversi quantity dulu.
     * Contoh: quantity_needed=150, unit_used=gram, ingredient.unit=kg, cost_per_unit=39000/kg
     * → konversi 150 gram = 0.15 kg → cost = 0.15 × 39.000 = Rp 5.850
     */
    public function getCostAttribute(): float
    {
        if (!$this->ingredient) return 0;

        $quantity = $this->quantity_needed;
        $unitUsed = $this->unit_used ?? $this->ingredient->unit;

        // Konversi quantity ke satuan asli bahan jika berbeda
        if ($unitUsed !== $this->ingredient->unit) {
            $factor = Ingredient::getConversionFactor($unitUsed, $this->ingredient->unit);
            $quantity = $quantity * $factor;
        }

        return round($this->ingredient->cost_per_unit * $quantity, 2);
    }

    /**
     * Relasi: Item/produk yang menggunakan bahan ini.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi: Bahan baku yang digunakan.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
