<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

/**
 * The header search, which runs across every department rather than within
 * one, as the filter panel's own search does.
 */
class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The term is spelled here exactly as the products print it. Matching a
     * Cyrillic term across cases is Postgres' job and cannot be asserted from
     * here - see the note on SearchController::results().
     */
    public function test_a_term_matches_the_printed_name_across_departments(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);
        $accessories = Category::factory()->create(['slug' => 'aksesoari']);

        $matchingCase = Product::factory()->create([
            'category_id' => $cases->id,
            'name' => 'Кожен калъф',
            'subtitle' => 'iPhone 15',
        ]);
        $matchingAccessory = Product::factory()->create([
            'category_id' => $accessories->id,
            'name' => 'Силиконов калъф',
            'subtitle' => null,
        ]);
        Product::factory()->create([
            'category_id' => $cases->id,
            'name' => 'Протектор за екран',
            'subtitle' => 'Samsung',
        ]);

        $this->get(route('search', ['q' => 'калъф']))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('storefront/Search')
                    ->where('term', 'калъф')
                    ->has('products', 2)
                    // Grouped by department, in the order the departments sit.
                    ->where('products.0.id', $matchingCase->id)
                    ->where('products.1.id', $matchingAccessory->id)
            );
    }

    public function test_a_term_matches_the_subtitle_and_ignores_case(): void
    {
        $category = Category::factory()->create();

        $wanted = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Силиконов гръб',
            'subtitle' => 'iPhone 15 Pro Max',
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Силиконов гръб',
            'subtitle' => 'Samsung Galaxy S23',
        ]);

        $this->get(route('search', ['q' => 'iphone']))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('products', 1)
                    ->where('products.0.id', $wanted->id)
            );
    }

    public function test_a_term_matches_the_brand_even_when_the_name_does_not(): void
    {
        $category = Category::factory()->create();
        $lattafa = Brand::factory()->create(['name' => 'Lattafa']);

        $wanted = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $lattafa->id,
            'name' => 'Asad',
            'subtitle' => 'Парфюмна вода',
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => Brand::factory()->create(['name' => 'Asdaaf'])->id,
            'name' => 'Ard Al Zaafaran',
            'subtitle' => null,
        ]);

        $this->get(route('search', ['q' => 'Lattafa']))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('products', 1)
                    ->where('products.0.id', $wanted->id)
            );
    }

    public function test_the_page_asks_for_a_term_rather_than_listing_everything(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        foreach ([route('search'), route('search', ['q' => '   '])] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertInertia(
                    fn (AssertableInertia $page) => $page
                        ->component('storefront/Search')
                        ->where('term', null)
                        ->has('products', 0)
                );
        }
    }

    /**
     * A wildcard is escaped rather than passed through to the query. What that
     * escape then matches is the driver's business - Postgres reads the
     * backslash and finds the literal sign, SQLite has no default escape and
     * finds nothing - so what is asserted here is only that a hand-typed `%`
     * cannot widen the search to the whole catalogue.
     */
    public function test_a_wildcard_in_the_term_cannot_widen_the_search(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Обикновен калъф',
            'subtitle' => null,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Протектор за екран',
            'subtitle' => null,
        ]);

        $this->get(route('search', ['q' => '%']))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('term', '%')
                    ->has('products', 0)
            );
    }
}
