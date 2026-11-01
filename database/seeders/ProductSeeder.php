<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Pasir Pasang', 'Pasir', 'm3', 180000, 120],
            ['Pasir Cor / Beton', 'Pasir', 'm3', 210000, 90],
            ['Pasir Urug', 'Pasir', 'm3', 130000, 200],
            ['Pasir Hitam (Lumajang)', 'Pasir', 'm3', 240000, 60],
            ['Pasir Halus / Plester', 'Pasir', 'm3', 195000, 45],
            ['Sirtu Biasa', 'Sirtu', 'm3', 150000, 150],
            ['Sirtu Ayak', 'Sirtu', 'm3', 170000, 80],
            ['Sirtu Urug (1 Rit)', 'Sirtu', 'rit', 650000, 30],
            ['Tinslah', 'Tinslah', 'm3', 160000, 70],
        ];
        foreach ($rows as [$name, $cat, $unit, $price, $stock]) {
            Product::firstOrCreate(['name' => $name], [
                'category' => $cat, 'unit' => $unit, 'price' => $price, 'stock' => $stock, 'min_stock' => 20,
            ]);
        }
    }
}
