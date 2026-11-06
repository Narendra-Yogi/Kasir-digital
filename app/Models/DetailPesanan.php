<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'notes',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'order_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'item_id');
    }
}

