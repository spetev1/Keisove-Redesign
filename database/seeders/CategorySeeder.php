<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The storefront's top-level departments. "Промоции" is deliberately
     * absent - it is a filtered view over these categories, not a department
     * of its own.
     *
     * The three departments the homepage leads on carry their own artwork. The
     * rest still borrow a representative product shot, which stands in until
     * photography for them lands.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Кейсове',
                'slug' => 'keisove',
                'tagline' => 'За всеки телефон. Всеки стил.',
                'image_path' => 'images/categories/keisove.jpg',
                'sort_order' => 1,
            ],
            [
                'name' => 'Парфюми',
                'slug' => 'parfyumi',
                'tagline' => 'Аромати, които оставят впечатление.',
                'image_path' => 'images/categories/parfyumi.png',
                'sort_order' => 2,
            ],
            [
                'name' => 'Аксесоари',
                'slug' => 'aksesoari',
                'tagline' => 'Детайлите, които правят разликата.',
                'image_path' => 'images/products/slushalki-hoco-w35-90h.jpg',
                'sort_order' => 3,
            ],
            [
                'name' => 'Протектори',
                'slug' => 'protektori',
                'tagline' => 'Защита за екрана и камерата.',
                'image_path' => 'images/categories/protektori.jpg',
                'sort_order' => 4,
            ],
            [
                'name' => 'Детски играчки',
                'slug' => 'detski-igrachki',
                'tagline' => 'Играчки, които заслужават разопаковане.',
                'image_path' => 'images/products/metalna-kola-bmw-z4-m40i.jpg',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
