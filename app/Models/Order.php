<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'total_amount',
        'payment_method',
        'cash_received',
        'change',
        'status',
    ];

    // --- Scope Query (Penyaringan Data) ---

    // Scope untuk mengambil transaksi yang berhasil/sukses
    public function scopeSukses($query)
    {
        return $query->where('status', 'success');
    }

    // Scope untuk mengambil transaksi yang dibatalkan
    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Scope untuk mengambil transaksi di antara dua tanggal tertentu
    public function scopeAntaraTanggal($query, $tanggalMulai, $tanggalSelesai)
    {
        return $query->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
    }

    // Scope untuk mengambil transaksi pada tanggal tertentu saja
    public function scopePerTanggal($query, $tanggal)
    {
        return $query->whereDate('created_at', $tanggal);
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
