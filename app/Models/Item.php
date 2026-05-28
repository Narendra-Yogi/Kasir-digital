<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
        'is_available',
        'image',
        'created_by',
        'updated_by'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
