<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

class Ingredient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'purchase_price',
        'purchase_quantity',
        'unit',
        'cost_per_unit',
        'created_by',
    ];

    /**
     * Auto-hitung cost_per_unit saat menyimpan data.
     * cost_per_unit = purchase_price / purchase_quantity
     * Contoh: Cabe 1kg (1000g) = Rp 40.000 → cost_per_unit = Rp 40/gram
     */
    #[Override]
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ingredient) {
            if ($ingredient->purchase_quantity > 0) {
                $ingredient->cost_per_unit = round($ingredient->purchase_price / $ingredient->purchase_quantity, 2);
            }
        });
    }

    /**
     * Mendapatkan harga per unit yang sudah diformat.
     */
    public function getFormattedCostPerUnitAttribute(): string
    {
        return 'Rp ' . number_format($this->cost_per_unit, 0, ',', '.') . '/' . $this->unit;
    }

    /**
     * Tabel konversi antar satuan.
     * Key: satuan asal, Value: [satuan tujuan => faktor pengali]
     */
    public const UNIT_CONVERSIONS = [
        'gram'  => ['kg' => 0.001],
        'kg'    => ['gram' => 1000],
        'ml'    => ['liter' => 0.001],
        'liter' => ['ml' => 1000],
    ];

    /**
     * Mendapatkan daftar satuan yang kompatibel dengan satuan bahan ini.
     * Contoh: bahan unit "kg" → ['kg', 'gram'], bahan unit "pcs" → ['pcs']
     *
     * @return array
     */
    public function getCompatibleUnits(): array
    {
        $units = [$this->unit];

        if (isset(self::UNIT_CONVERSIONS[$this->unit])) {
            $units = array_merge($units, array_keys(self::UNIT_CONVERSIONS[$this->unit]));
        }

        return $units;
    }

    /**
     * Mendapatkan faktor konversi dari satu satuan ke satuan lain.
     * Contoh: getConversionFactor('gram', 'kg') = 0.001
     *         getConversionFactor('kg', 'gram') = 1000
     *         getConversionFactor('gram', 'gram') = 1
     *
     * @param string $fromUnit Satuan asal
     * @param string $toUnit Satuan tujuan
     * @return float Faktor konversi (return 1 jika satuan sama atau tidak bisa dikonversi)
     */
    public static function getConversionFactor(string $fromUnit, string $toUnit): float
    {
        if ($fromUnit === $toUnit) {
            return 1.0;
        }

        return self::UNIT_CONVERSIONS[$fromUnit][$toUnit] ?? 1.0;
    }

    /**
     * Relasi: Bahan ini digunakan di banyak produk (melalui item_ingredients).
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_ingredients')
            ->withPivot('quantity_needed')
            ->withTimestamps();
    }

    /**
     * Relasi: Detail penggunaan bahan di produk-produk.
     */
    public function itemIngredients(): HasMany
    {
        return $this->hasMany(ItemIngredient::class);
    }
}
