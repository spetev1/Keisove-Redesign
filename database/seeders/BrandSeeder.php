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
     *
     * Samsung and Huawei are here as makers rather than as handsets: the store
     * sells their own chargers and replacement batteries. Which handset a
     * product fits is `device_family` on the product, not a brand.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Keisove', 'slug' => 'keisove'],
            ['name' => 'Spigen', 'slug' => 'spigen'],
            ['name' => 'Etteri', 'slug' => 'etteri'],
            ['name' => 'Tech-Protect', 'slug' => 'tech-protect'],
            ['name' => 'Hoco', 'slug' => 'hoco'],
            ['name' => 'Borofone', 'slug' => 'borofone'],
            ['name' => 'LDNIO', 'slug' => 'ldnio'],
            ['name' => 'Dudao', 'slug' => 'dudao'],
            ['name' => 'XO', 'slug' => 'xo'],
            ['name' => 'Maxlife', 'slug' => 'maxlife'],
            ['name' => 'Phone Planet', 'slug' => 'phone-planet'],
            ['name' => 'AKG', 'slug' => 'akg'],
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Huawei', 'slug' => 'huawei'],
            ['name' => 'Valdus', 'slug' => 'valdus'],
            ['name' => 'SanDisk', 'slug' => 'sandisk'],
            ['name' => 'GOODRAM', 'slug' => 'goodram'],
            ['name' => 'Toshiba', 'slug' => 'toshiba'],
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
