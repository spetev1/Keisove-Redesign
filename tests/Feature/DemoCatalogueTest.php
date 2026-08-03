<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\BrandSeeder;
use Database\Seeders\CategoryArtworkSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
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
            CategoryArtworkSeeder::class,
        ]);
    }

    public function test_the_seeded_catalogue_fills_every_homepage_section(): void
    {
        // Derived, so adding a department is not a spurious failure here. The
        // homepage grid and the nav row carry departments, not every category.
        $departmentCount = Category::departments()->count();

        // The grid never loads more than it is capped at, so what the seeded
        // catalogue puts in it is the smaller of the two figures.
        $newProductCount = min(
            8,
            Product::where('is_new', true)->count(),
        );

        $this->assertGreaterThanOrEqual(5, $departmentCount);

        // Enough to fill the grid the design lays out, which is the point of
        // flagging any of them as new in the seeder.
        $this->assertSame(8, $newProductCount);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('newProducts', $newProductCount)
                    ->has('storefrontCategories', $departmentCount)
                    // Two columns of three behind the hero copy.
                    ->has('heroCollage', 6)
                    // Both cards name something real, so neither renders with a
                    // blank line where the design has a handset or a price.
                    ->whereNot('spotlights.newArrivals.subject', '')
                    ->whereNot('spotlights.bestseller.subject', '')
                    ->whereNot('spotlights.bestseller.fromPrice', null)
            );
    }

    /**
     * The grid is the design's twelve tiles in the design's order, which is not
     * the nav row's order and not the taxonomy's.
     *
     * The perfumes are eighth on purpose: seven tiles to a row from `xl`, so
     * eighth opens the second row and the violet tile lands bottom left. Moving
     * it moves the one piece of colour in the grid, so it is worth pinning.
     */
    public function test_the_homepage_grid_follows_the_designs_tile_order(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('featuredCategories', 12)
                    ->where('featuredCategories.0.slug', 'silikonovi-garbove')
                    ->where('featuredCategories.7.slug', 'parfyumi')
                    ->where('featuredCategories.11.slug', 'kalafi-za-tableti')
                    // A department's tile counts its whole shelf, not the
                    // nothing it holds itself.
                    ->where('featuredCategories.3.slug', 'zaryadni')
                    ->where('featuredCategories.3.productCount', 23)
            );
    }

    /**
     * The brand panel, the new-arrivals link and the featured tile name these
     * slugs outright, so renaming one in the seeder would point the pitch at a
     * 404 without anything else failing.
     */
    public function test_the_categories_the_homepage_names_are_real_pages(): void
    {
        foreach (['kalafi', 'protektori', 'parfyumi'] as $slug) {
            $category = Category::firstWhere('slug', $slug);

            $this->assertNotNull(
                $category,
                "The homepage links to a missing category [{$slug}].",
            );

            $this->get(route('category.show', $category))->assertOk();
        }
    }

    /**
     * A department holds nothing itself - its products sit on its children - so
     * what has to be non-empty is the subtree, not the row.
     */
    public function test_every_seeded_category_has_products_to_show(): void
    {
        foreach (Category::all() as $category) {
            $this->assertGreaterThan(
                0,
                Product::whereIn(
                    'category_id',
                    $category->subtreeIds()
                )->count(),
                "Category [{$category->slug}] would render an empty page.",
            );
        }
    }

    /**
     * Every department page must be reachable, and must list the products of
     * whatever sits beneath it rather than the nothing it holds itself.
     */
    public function test_every_department_page_lists_its_childrens_products(): void
    {
        $departments = Category::departments()->with('children')->get();

        $this->assertNotEmpty($departments);

        foreach ($departments as $department) {
            $expected = Product::whereIn(
                'category_id',
                $department->subtreeIds()
            )->count();

            $this->get(route('category.show', $department))
                ->assertOk()
                ->assertInertia(
                    fn (AssertableInertia $page) => $page
                        ->where('category.slug', $department->slug)
                        ->has('products', $expected)
                        ->has(
                            'category.children',
                            $department->children->count(),
                        )
                );
        }
    }

    /**
     * The taxonomy is deliberately one level deep: the handset and construction
     * splits the live store carries as categories are filters here, and a third
     * level would be the point at which the subtree query needs rethinking.
     */
    public function test_the_taxonomy_is_no_deeper_than_two_levels(): void
    {
        $grandchildren = Category::query()
            ->whereHas('parent', fn ($parent) => $parent->whereNotNull('parent_id'))
            ->pluck('slug');

        $this->assertEmpty(
            $grandchildren,
            'These categories sit three levels deep: '.$grandchildren->implode(', '),
        );
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
