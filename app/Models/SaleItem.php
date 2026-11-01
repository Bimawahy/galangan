<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'product_name', 'unit', 'qty', 'price', 'subtotal'];

    protected function casts(): array
    {
        return ['qty' => 'float'];
    }
}
