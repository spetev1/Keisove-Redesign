<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The brands the demo catalogue actually carries, as they appear on the
     * live store.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Keisove', 'slug' => 'keisove'],
            ['name' => 'Spigen', 'slug' => 'spigen'],
            ['name' => 'Etteri', 'slug' => 'etteri'],
            ['name' => 'Hoco', 'slug' => 'hoco'],
            ['name' => 'Dudao', 'slug' => 'dudao'],
            ['name' => 'XO', 'slug' => 'xo'],
            ['name' => 'Valdus', 'slug' => 'valdus'],
            ['name' => 'Lattafa', 'slug' => 'lattafa'],
            ['name' => 'Asdaaf', 'slug' => 'asdaaf'],
            ['name' => 'Fragrance World', 'slug' => 'fragrance-world'],
            ['name' => 'French Avenue', 'slug' => 'french-avenue'],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['slug' => $brand['slug']], $brand);
        }
    }
}
