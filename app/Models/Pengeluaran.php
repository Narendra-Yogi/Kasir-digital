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
    ];

    /**
     * Relasi ke model User (Siapa yang mencatat pengeluaran)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
