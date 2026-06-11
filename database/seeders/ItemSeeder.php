<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Sugar', 'Rice', 'Flour', 'Salt', 'Oil'];

        foreach ($items as $name) {
            Item::firstOrCreate(['name' => $name]);
        }
    }
}
