<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;

    protected $table = 'kategori';

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Menu::class, 'category_id');
    }
}

