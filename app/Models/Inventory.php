<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

class Inventory extends Model
{
    protected $fillable = [
        'date',
        'item_name',
        'old_stock',
        'new_stock',
        'sold',
        'remaining_stock',
        'created_by'
    ];

    #[Override]
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            $inventory->remaining_stock = ($inventory->old_stock + $inventory->new_stock) - $inventory->sold;
        }) ;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
