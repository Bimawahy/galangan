<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const CATEGORIES = ['Pasir', 'Sirtu', 'Tinslah', 'Lainnya'];
    public const UNITS = ['m3', 'rit', 'ton'];

    protected $fillable = ['name', 'category', 'unit', 'price', 'stock', 'min_stock', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'stock' => 'float', 'min_stock' => 'float'];
    }

    public function isLow(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}
