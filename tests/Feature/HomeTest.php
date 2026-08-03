<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Support\DeviceFamily;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
                    ->has('deviceFamilies')
                    ->has('newProducts')
                    ->has('heroCollage')
                    ->has('spotlights.newArrivals')
                    ->has('spotlights.bestseller')
                    ->has('saleEndsAt')
            );
    }

    /**
     * The departments the category grid lays out are the shared ones the header
     * already carries, so the page does not ask for them a second time.
     */
    public function test_the_category_grid_reads_the_shared_departments_rather_than_its_own(): void
    {
        $cases = Category::factory()->create([
            'slug' => 'kalafi',
            'sort_order' => 1,
        ]);
        Product::factory()->count(3)->create(['category_id' => $cases->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->missing('categories')
                    ->where('storefrontCategories.0.slug', 'kalafi')
                    ->where('storefrontCategories.0.productCount', 3)
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

        // The logo is loaded this way, so nothing found would mean the pattern
        // stopped matching rather than that the art is in order.
        $this->assertGreaterThanOrEqual(1, $referenced);
    }

    /**
     * The brand panel names handsets, so it is fed from the enum the category
     * filters are built from rather than a list of its own. Adding a family must
     * reach both at once.
     */
    public function test_the_brand_panel_is_fed_from_the_handset_families_the_filters_use(): void
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

    public function test_the_new_products_grid_carries_only_new_arrivals_newest_first(): void
    {
        $category = Category::factory()->create(['slug' => 'kalafi']);

        $older = Product::factory()->isNew()->create([
            'category_id' => $category->id,
        ]);
        $newer = Product::factory()->isNew()->create([
            'category_id' => $category->id,
        ]);
        Product::factory()->create(['category_id' => $category->id]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('newProducts', 2)
                    ->where('newProducts.0.id', $newer->id)
                    ->where('newProducts.1.id', $older->id)
            );
    }

    /**
     * The catalogue is seeded department by department, so newest-first alone
     * would hand every slot to whichever went in last. The row deals across the
     * departments instead, or a phone accessory shop opens on eight toys.
     */
    public function test_the_new_products_grid_spreads_across_departments(): void
    {
        $toys = Category::factory()->create(['slug' => 'detski-igrachki']);
        $cases = Category::factory()->create(['slug' => 'kalafi']);

        // The cases go in first, so every toy outranks them on id.
        $caseIds = Product::factory()
            ->isNew()
            ->count(3)
            ->create(['category_id' => $cases->id])
            ->pluck('id');

        Product::factory()->isNew()->count(6)->create([
            'category_id' => $toys->id,
        ]);

        $this->get(route('home'))->assertInertia(
            function (AssertableInertia $page) use ($caseIds): void {
                $shown = collect($page->toArray()['props']['newProducts'])
                    ->pluck('id');

                $cases = $shown->intersect($caseIds);

                $this->assertCount(
                    3,
                    $cases,
                    'Every case should have made the grid, not been crowded out.',
                );
                // Dealt alternately, so the row does not open on one department.
                $this->assertNotSame(
                    $shown->take(3)->all(),
                    $shown->take(3)->sort()->values()->all(),
                );
            }
        );
    }

    /**
     * The grid is two rows deep by design; a catalogue with more than that
     * behind it must not push the sections below off the page.
     */
    public function test_the_new_products_grid_is_capped(): void
    {
        $category = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->isNew()->count(12)->create([
            'category_id' => $category->id,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->has('newProducts', 8)
            );
    }

    /**
     * Every card the grid renders needs its department, because the row's filter
     * pills are built from whichever departments are actually present.
     */
    public function test_each_new_product_names_its_department(): void
    {
        $category = Category::factory()->create(['slug' => 'aksesoari']);

        Product::factory()->isNew()->create(['category_id' => $category->id]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->where(
                    'newProducts.0.categorySlug',
                    'aksesoari',
                )
            );
    }

    /**
     * A product with no photograph would leave a hole in the collage, so the
     * hero only ever draws from what has one.
     */
    public function test_the_hero_collage_only_carries_photographed_products(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->create([
            'category_id' => $cases->id,
            'image_path' => 'images/products/case.jpg',
        ]);
        Product::factory()->create([
            'category_id' => $cases->id,
            'image_path' => null,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('heroCollage', 1)
                    ->where('heroCollage.0', asset('images/products/case.jpg'))
            );
    }

    public function test_the_hero_collage_is_capped_at_what_its_two_columns_hold(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()
            ->count(9)
            ->sequence(fn ($sequence) => [
                'image_path' => "images/products/case-{$sequence->index}.jpg",
            ])
            ->create(['category_id' => $cases->id]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->has('heroCollage', 6)
            );
    }

    /**
     * The collage is meant to be a wall of cases, but a demo whose lead
     * department is thin should still fill it rather than show the violet
     * through the gaps.
     */
    public function test_the_hero_collage_is_topped_up_from_other_departments(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);
        $perfumes = Category::factory()->create(['slug' => 'parfyumi']);

        Product::factory()->count(2)->sequence(
            fn ($sequence) => [
                'image_path' => "images/products/case-{$sequence->index}.jpg",
            ]
        )->create(['category_id' => $cases->id]);

        Product::factory()->count(5)->sequence(
            fn ($sequence) => [
                'image_path' => "images/products/scent-{$sequence->index}.jpg",
            ]
        )->create(['category_id' => $perfumes->id]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('heroCollage', 6)
                    // Cases lead, whatever tops the collage up follows them.
                    ->where(
                        'heroCollage.0',
                        asset('images/products/case-0.jpg'),
                    )
            );
    }

    /**
     * The card beside the hero names a handset rather than a fixed model, and
     * opens the department already narrowed to it.
     */
    /**
     * The card names the handset the case department has most recently added and
     * opens that department narrowed to it.
     */
    public function test_the_new_arrivals_card_names_the_latest_handset_and_links_to_it(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->isNew()->create([
            'category_id' => $cases->id,
            'device_family' => DeviceFamily::Samsung,
            'image_path' => 'images/products/galaxy.jpg',
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('spotlights.newArrivals.subject', 'Samsung')
                    ->where(
                        'spotlights.newArrivals.imageUrl',
                        asset('images/products/galaxy.jpg'),
                    )
                    ->where(
                        'spotlights.newArrivals.href',
                        route('category.show', [
                            'category' => 'kalafi',
                            'devices' => ['samsung'],
                        ]),
                    )
            );
    }

    /**
     * A department that also has children is the harder case: the card has to
     * reach into them rather than only at the row it was pointed at.
     */
    public function test_the_new_arrivals_card_reaches_into_a_departments_children(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);
        $child = Category::factory()->create([
            'slug' => 'silikonovi-garbove',
            'parent_id' => $cases->id,
        ]);

        Product::factory()->isNew()->create([
            'category_id' => $child->id,
            'device_family' => DeviceFamily::Honor,
            'image_path' => 'images/products/honor.jpg',
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->where(
                    'spotlights.newArrivals.subject',
                    'Honor',
                )
            );
    }

    /**
     * Nothing new in the department is a legitimate state, so the card falls
     * back to a handset the store does stock rather than naming none.
     */
    public function test_the_new_arrivals_card_falls_back_when_nothing_is_new(): void
    {
        $cases = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->create([
            'category_id' => $cases->id,
            'device_family' => DeviceFamily::Xiaomi,
            'image_path' => 'images/products/redmi.jpg',
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->where(
                    'spotlights.newArrivals.subject',
                    'Xiaomi',
                )
            );
    }

    /**
     * The design writes a from-price into the perfume card. It is taken from the
     * catalogue instead, so the homepage cannot promise an entry price the
     * department does not have.
     */
    public function test_the_bestseller_card_carries_the_departments_cheapest_price(): void
    {
        $perfumes = Category::factory()->create([
            'slug' => 'parfyumi',
            'name' => 'Парфюми',
        ]);

        Product::factory()->create([
            'category_id' => $perfumes->id,
            'price_in_stotinki' => 8990,
            'image_path' => 'images/products/dear.jpg',
        ]);
        Product::factory()->create([
            'category_id' => $perfumes->id,
            'price_in_stotinki' => 2490,
            'image_path' => 'images/products/cheap.jpg',
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('spotlights.bestseller.subject', 'Парфюми')
                    ->where('spotlights.bestseller.fromPrice', '24.90 лв.')
                    ->where(
                        'spotlights.bestseller.imageUrl',
                        asset('images/products/cheap.jpg'),
                    )
            );
    }

    public function test_the_countdown_runs_to_the_configured_deadline(): void
    {
        config(['demo.sale_ends_at' => '2030-06-01 18:00:00']);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->where(
                    'saleEndsAt',
                    Carbon::parse('2030-06-01 18:00:00')->toIso8601String(),
                )
            );
    }

    /**
     * A demo link is opened over weeks, so with no deadline configured the band
     * must still be counting down to something rather than showing an expired
     * promotion.
     */
    public function test_the_countdown_falls_back_to_a_deadline_still_ahead(): void
    {
        config(['demo.sale_ends_at' => null]);

        $response = $this->get(route('home'));

        $endsAt = Carbon::parse(
            $response->getOriginalContent()->getData()['page']['props']['saleEndsAt']
        );

        $this->assertTrue($endsAt->isFuture());
    }

    public function test_a_discounted_product_is_sent_with_a_struck_price_and_a_rounded_badge(): void
    {
        $category = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->isNew()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 2290,
            'compare_at_price_in_stotinki' => 2690,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('newProducts.0.price', '22.90 лв.')
                    ->where('newProducts.0.compareAtPrice', '26.90 лв.')
                    ->where('newProducts.0.discountPercentage', 15)
            );
    }

    public function test_a_full_price_product_carries_no_discount(): void
    {
        $accessories = Category::factory()->create(['slug' => 'aksesoari']);

        Product::factory()->isNew()->create([
            'category_id' => $accessories->id,
            'price_in_stotinki' => 2490,
            'compare_at_price_in_stotinki' => null,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('newProducts.0.price', '24.90 лв.')
                    ->where('newProducts.0.compareAtPrice', null)
                    ->where('newProducts.0.discountPercentage', null)
            );
    }

    /**
     * The card prints both figures in euro under the lev row, so both have to
     * arrive - and the struck one has to stay absent when there is no discount.
     */
    public function test_a_product_card_carries_both_prices_in_euro(): void
    {
        $category = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->isNew()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 2245,
            'compare_at_price_in_stotinki' => 4490,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('newProducts.0.priceInEur', '11.48 €')
                    ->where('newProducts.0.compareAtPriceInEur', '22.96 €')
            );
    }

    public function test_a_full_price_product_carries_no_struck_euro_figure(): void
    {
        $category = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->isNew()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 2490,
            'compare_at_price_in_stotinki' => null,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page->where(
                    'newProducts.0.compareAtPriceInEur',
                    null,
                )
            );
    }

    /**
     * The card's eyebrow is the handset family, named rather than sent as a raw
     * slug so the card and the filters cannot print it differently.
     */
    public function test_a_product_card_carries_its_handset_family_label(): void
    {
        $category = Category::factory()->create(['slug' => 'kalafi']);

        Product::factory()->isNew()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::IPhone,
        ]);
        Product::factory()->isNew()->create([
            'category_id' => $category->id,
            'device_family' => null,
        ]);

        $this->get(route('home'))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('newProducts.1.deviceFamilyLabel', 'iPhone')
                    ->where('newProducts.0.deviceFamilyLabel', null)
            );
    }
}
