<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['ABC', 'XYZ', 'Premium', 'Standard'];

        foreach ($brands as $name) {
            Brand::firstOrCreate(['name' => $name]);
        }
    }
}
