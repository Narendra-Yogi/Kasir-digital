<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    // Menentukan nama tabel secara eksplisit dalam bahasa Indonesia
    protected $table = 'pengeluaran';

    protected $fillable = [
        'date',
        'item_name',
        'category',
        'amount',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Kategori pengeluaran mode sederhana UMKM (v2).
     * Mapping lama: bahan -> belanja_harian, logistik -> operasional.
     */
    public const KATEGORI = ['belanja_harian', 'operasional', 'gaji', 'lainnya'];

    public const KATEGORI_LABEL = [
        'belanja_harian' => 'Belanja Harian',
        'operasional' => 'Operasional',
        'gaji' => 'Gaji',
        'lainnya' => 'Lainnya',
    ];

    public static function labelKategori(string $kategori): string
    {
        return self::KATEGORI_LABEL[$kategori] ?? ucfirst($kategori);
    }

    /**
     * Relasi ke model Pengguna (Siapa yang mencatat pengeluaran)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'created_by');
    }
}
