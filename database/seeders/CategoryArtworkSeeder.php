<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryArtworkSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Gives every category still without artwork a representative product shot.
     *
     * Only three departments have photography of their own so far. Rather than
     * naming a product file per category in the taxonomy - a path that goes
     * stale the moment a product is renamed or its image arrives as a .png
     * instead of a .jpg - each category borrows the first shot from its own
     * shelf. The tiles are the artwork on the homepage, so a category with
     * nothing to show would render as a bare panel.
     *
     * Runs after the products, because that is what it reads.
     */
    public function run(): void
    {
        $categories = Category::query()
            ->whereNull('image_path')
            ->with('children')
            ->ordered()
            ->get();

        foreach ($categories as $category) {
            $borrowed = Product::query()
                ->whereIn('category_id', $category->subtreeIds())
                ->whereNotNull('image_path')
                ->orderBy('sort_order')
                ->value('image_path');

            if ($borrowed !== null) {
                $category->update(['image_path' => $borrowed]);
            }
        }
    }
}
