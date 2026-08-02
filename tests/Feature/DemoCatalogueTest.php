<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * The seeded catalogue is what the client sees in the pitch, so the things
 * that would visibly break it are worth asserting rather than eyeballing.
 */
class DemoCatalogueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
        ]);
    }

    public function test_the_seeded_catalogue_fills_every_homepage_row(): void
    {
        // Derived, so adding a department is not a spurious failure here.
        $categoryCount = Category::count();
        $accessoryCount = Product::whereRelation(
            'category',
            'slug',
            'aksesoari',
        )->count();
        // The row draws from the phone departments only, and never loads more
        // than it is capped at, so the expected count is both of those at once.
        $promotionCount = min(10, Product::query()
            ->whereHas(
                'category',
                fn (Builder $category) => $category->whereIn(
                    'slug',
                    ['keisove', 'protektori', 'aksesoari'],
                ),
            )
            ->whereColumn(
                'compare_at_price_in_stotinki',
                '>',
                'price_in_stotinki',
            )
            ->count());

        $this->assertGreaterThanOrEqual(3, $categoryCount);
        $this->assertGreaterThan(0, $accessoryCount);

        // Enough to fill the visible part of the row and leave something to
        // scroll to, which is the point of seeding them at all.
        $this->assertGreaterThanOrEqual(6, $promotionCount);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('promotions', $promotionCount)
                    ->has('accessories', $accessoryCount)
                    ->has('categories', $categoryCount)
            );
    }

    /**
     * The hero's call to action, the three department tiles and both carousels
     * name these slugs outright, so renaming one in the seeder would point the
     * pitch at a 404 without anything else failing.
     */
    public function test_the_categories_the_homepage_names_are_real_pages(): void
    {
        foreach (['keisove', 'protektori', 'parfyumi'] as $slug) {
            $category = Category::firstWhere('slug', $slug);

            $this->assertNotNull(
                $category,
                "The homepage links to a missing category [{$slug}].",
            );

            $this->get(route('category.show', $category))->assertOk();
        }
    }

    public function test_every_seeded_category_has_products_to_show(): void
    {
        foreach (Category::all() as $category) {
            $this->assertGreaterThan(
                0,
                $category->products()->count(),
                "Category [{$category->slug}] would render an empty page.",
            );
        }
    }

    /**
     * The homepage tiles are the artwork - the picture is the card, not
     * something framed inside it - so a missing file leaves a bare panel where
     * a department should be.
     */
    public function test_every_seeded_category_ships_with_artwork_that_exists_on_disk(): void
    {
        $categories = Category::all();

        $this->assertNotEmpty($categories);

        foreach ($categories as $category) {
            $this->assertNotNull(
                $category->image_path,
                "Category [{$category->slug}] has no artwork.",
            );
            $this->assertFileExists(
                public_path($category->image_path),
                "Category [{$category->slug}] points at missing artwork.",
            );
        }
    }

    public function test_every_seeded_product_ships_with_an_image_that_exists_on_disk(): void
    {
        $products = Product::all();

        $this->assertNotEmpty($products);

        foreach ($products as $product) {
            $this->assertNotNull(
                $product->image_path,
                "Product [{$product->slug}] has no image.",
            );
            $this->assertFileExists(
                public_path($product->image_path),
                "Product [{$product->slug}] points at a missing image.",
            );
        }
    }

    public function test_a_struck_price_is_always_higher_than_what_the_shopper_pays(): void
    {
        $products = Product::whereNotNull('compare_at_price_in_stotinki')->get();

        $this->assertNotEmpty(
            $products,
            'The demo needs at least one discounted product to show the badge.',
        );

        foreach ($products as $product) {
            $this->assertTrue(
                $product->isDiscounted(),
                "Product [{$product->slug}] carries a struck price that is not a discount.",
            );
        }
    }
}
