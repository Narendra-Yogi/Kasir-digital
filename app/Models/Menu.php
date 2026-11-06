<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
        'is_available',
        'image',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }
}

