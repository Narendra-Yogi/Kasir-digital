<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BukuKasHarian extends Model
{
    // Menentukan nama tabel secara eksplisit dalam bahasa Indonesia
    protected $table = 'buku_kas_harian';

    protected $fillable = [
        'date',
        'user_id',
        'starting_cash',
        'system_cash_sales',
        'system_qris_sales',
        'system_expenses',
        'actual_cash',
        'discrepancy',
        'notes',
    ];

    /**
     * Relasi ke model User (Kasir yang bertugas menutup kas)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
