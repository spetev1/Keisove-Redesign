<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Support\DeviceFamily;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_renders_the_storefront(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('storefront/Home')
                    ->has('categories')
                    ->has('promotions')
                    ->has('newProducts')
                    ->has('accessories')
            );
    }

    /**
     * The hero photograph and the logo are served straight from `public/`
     * rather than bundled by Vite, so nothing fails the build when one goes
     * missing - the pitch just opens on an empty band where it should be.
     */
    public function test_every_image_the_storefront_hardcodes_exists_on_disk(): void
    {
        $components = glob(
            base_path('resources/js/components/storefront/*.vue')
        ) ?: [];

        $this->assertNotEmpty($components);

        $referenced = 0;

        foreach ($components as $component) {
            preg_match_all(
                '/src="(\/images\/[^"]+)"/',
                (string) file_get_contents($component),
                $matches,
            );

            foreach ($matches[1] as $path) {
                $referenced++;

                $this->assertFileExists(
                    public_path($path),
                    basename($component)." points at a missing image [{$path}].",
                );
            }
        }

        // The hero and the logo are both loaded this way, so nothing found
        // would mean the pattern stopped matching rather than that the art is
        // in order.
        $this->assertGreaterThanOrEqual(2, $referenced);
    }

    /**
     * The brand strip under the hero names handsets, so it is fed from the enum
     * the category filters are built from rather than a list of its own. Adding
     * a family must reach both at once.
     */
    public function test_the_brand_strip_is_fed_from_the_handset_families_the_filters_use(): void
    {
        $expected = array_map(
            fn (DeviceFamily $family): array => [
                'value' => $family->value,
                'label' => $family->label(),
            ],
            DeviceFamily::cases(),
        );

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('deviceFamilies', count(DeviceFamily::cases()))
                    ->where('deviceFamilies', $expected)
            );
    }

    public function test_the_promotions_row_carries_only_discounted_products_biggest_saving_first(): void
    {
        $category = Category::factory()->create();

        $smallSaving = Product::factory()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 2000,
            'compare_at_price_in_stotinki' => 2500,
        ]);
        $bigSaving = Product::factory()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 5000,
            'compare_at_price_in_stotinki' => 9000,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'compare_at_price_in_stotinki' => null,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('promotions', 2)
                    ->where('promotions.0.id', $bigSaving->id)
                    ->where('promotions.1.id', $smallSaving->id)
            );
    }

    public function test_the_new_row_carries_only_what_is_marked_new(): void
    {
        $category = Category::factory()->create();

        $arrival = Product::factory()->create([
            'category_id' => $category->id,
            'is_new' => true,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'is_new' => false,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('newProducts', 1)
                    ->where('newProducts.0.id', $arrival->id)
            );
    }

    public function test_the_accessories_row_carries_only_that_department(): void
    {
        $accessories = Category::factory()->create(['slug' => 'aksesoari']);
        $cases = Category::factory()->create(['slug' => 'keisove']);

        $accessory = Product::factory()->create([
            'category_id' => $accessories->id,
        ]);
        Product::factory()->create(['category_id' => $cases->id]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('accessories', 1)
                    ->where('accessories.0.id', $accessory->id)
            );
    }

    public function test_a_discounted_product_is_sent_with_a_struck_price_and_a_rounded_badge(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 2290,
            'compare_at_price_in_stotinki' => 2690,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('promotions.0.price', '22.90 лв.')
                    ->where('promotions.0.compareAtPrice', '26.90 лв.')
                    ->where('promotions.0.discountPercentage', 15)
            );
    }

    public function test_a_full_price_product_carries_no_discount(): void
    {
        $accessories = Category::factory()->create(['slug' => 'aksesoari']);

        Product::factory()->create([
            'category_id' => $accessories->id,
            'price_in_stotinki' => 2490,
            'compare_at_price_in_stotinki' => null,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('accessories.0.price', '24.90 лв.')
                    ->where('accessories.0.compareAtPrice', null)
                    ->where('accessories.0.discountPercentage', null)
            );
    }
}
