<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Produksi: set SEED_PASSWORD di .env sebelum seeding, lalu ganti password lewat aplikasi.
        foreach (['admin' => 'Administrator', 'owner' => 'Owner', 'master' => 'Master'] as $role => $name) {
            User::updateOrCreate(
                ['username' => $role],
                [
                    'name' => $name,
                    'email' => "{$role}@example.com",
                    'password' => env('SEED_PASSWORD', 'password'),
                    'role' => $role,
                ]
            );
        }
        $this->call(ProductSeeder::class);
    }
}
