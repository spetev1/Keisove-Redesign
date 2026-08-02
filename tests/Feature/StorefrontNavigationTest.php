<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\CaseType;
use App\Support\DeviceFamily;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class StorefrontNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_category_page_lists_only_its_own_products(): void
    {
        $cases = Category::factory()->create(['slug' => 'keisove']);
        $perfumes = Category::factory()->create(['slug' => 'parfyumi']);

        $ownProduct = Product::factory()->create(['category_id' => $cases->id]);
        Product::factory()->create(['category_id' => $perfumes->id]);

        $this->get(route('category.show', $cases))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('storefront/Category')
                    ->where('category.slug', 'keisove')
                    ->has('products', 1)
                    ->where('products.0.id', $ownProduct->id)
            );
    }

    public function test_a_category_page_lists_products_in_their_seeded_order(): void
    {
        $category = Category::factory()->create();

        $second = Product::factory()->create([
            'category_id' => $category->id,
            'sort_order' => 2,
        ]);
        $first = Product::factory()->create([
            'category_id' => $category->id,
            'sort_order' => 1,
        ]);

        $this->get(route('category.show', $category))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('products.0.id', $first->id)
                    ->where('products.1.id', $second->id)
            );
    }

    public function test_a_product_page_renders_its_detail_copy(): void
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create(['name' => 'Lattafa']);

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'description' => 'Кратко описание.',
            'price_in_stotinki' => 7495,
        ]);

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('storefront/Product')
                    ->where('product.slug', $product->slug)
                    ->where('product.description', 'Кратко описание.')
                    ->where('product.brandName', 'Lattafa')
                    ->where('product.categoryName', $category->name)
                    ->where('product.price', '74.95 лв.')
                    ->has('relatedProducts')
            );
    }

    public function test_related_products_share_the_category_and_exclude_the_product_itself(): void
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        $product = Product::factory()->create(['category_id' => $category->id]);
        $sibling = Product::factory()->create(['category_id' => $category->id]);
        Product::factory()->create(['category_id' => $otherCategory->id]);

        $this->get(route('product.show', $product))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('relatedProducts', 1)
                    ->where('relatedProducts.0.id', $sibling->id)
            );
    }

    public function test_the_header_category_menu_is_shared_with_every_storefront_page(): void
    {
        $cases = Category::factory()->create([
            'slug' => 'keisove',
            'sort_order' => 1,
        ]);
        Category::factory()->create(['slug' => 'parfyumi', 'sort_order' => 2]);
        Product::factory()->count(3)->create(['category_id' => $cases->id]);

        foreach ([route('home'), route('category.show', $cases)] as $url) {
            $this->get($url)->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('storefrontCategories', 2)
                    ->where('storefrontCategories.0.slug', 'keisove')
                    ->where('storefrontCategories.0.productCount', 3)
            );
        }
    }

    public function test_a_category_can_be_narrowed_to_one_handset_family(): void
    {
        $cases = Category::factory()->create(['slug' => 'keisove']);

        $iphoneCase = Product::factory()->create([
            'category_id' => $cases->id,
            'name' => 'Силиконов гръб',
            'subtitle' => 'iPhone 15 · черен',
        ]);
        Product::factory()->create([
            'category_id' => $cases->id,
            'name' => 'Силиконов гръб',
            'subtitle' => 'Samsung Galaxy S23 · черен',
        ]);

        $this->get(route('category.show', $cases).'?q=iPhone')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('filters.q', 'iPhone')
                    ->has('products', 1)
                    ->where('products.0.id', $iphoneCase->id)
            );
    }

    public function test_an_unfiltered_category_reports_no_filter(): void
    {
        $cases = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $cases->id]);

        $this->get(route('category.show', $cases))
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('filters.q', null)
                    ->where('filters.sort', 'default')
                    ->where('filters.onSale', false)
                    ->has('products', 2)
            );
    }

    public function test_a_category_can_be_filtered_by_handset_price_and_offer(): void
    {
        $category = Category::factory()->create();

        $wanted = Product::factory()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::IPhone,
            'price_in_stotinki' => 4000,
            'compare_at_price_in_stotinki' => 8000,
        ]);
        // Right handset and price, but not discounted.
        Product::factory()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::IPhone,
            'price_in_stotinki' => 4000,
            'compare_at_price_in_stotinki' => null,
        ]);
        // Right handset and discounted, but priced out of the range.
        Product::factory()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::IPhone,
            'price_in_stotinki' => 9000,
            'compare_at_price_in_stotinki' => 18000,
        ]);
        // Discounted and in range, but the wrong handset.
        Product::factory()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::Samsung,
            'price_in_stotinki' => 4000,
            'compare_at_price_in_stotinki' => 8000,
        ]);

        $url = route('category.show', $category).'?devices[]=iphone&min=30&max=50&sale=1';

        $this->get($url)
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('products', 1)
                    ->where('products.0.id', $wanted->id)
                    ->where('filters.devices', ['iphone'])
                    ->where('filters.minPrice', 30)
                    ->where('filters.maxPrice', 50)
                    ->where('filters.onSale', true)
            );
    }

    public function test_a_category_can_be_filtered_by_case_type(): void
    {
        $category = Category::factory()->create();

        $leather = Product::factory()->create([
            'category_id' => $category->id,
            'case_type' => CaseType::Leather,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'case_type' => CaseType::Silicone,
        ]);

        $this->get(route('category.show', $category).'?types[]=kozhen')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('products', 1)
                    ->where('products.0.id', $leather->id)
                    ->where('filters.types', ['kozhen'])
            );
    }

    public function test_a_department_that_fits_no_handset_can_still_be_filtered_by_brand(): void
    {
        $perfumes = Category::factory()->create(['slug' => 'parfyumi']);
        $lattafa = Brand::factory()->create([
            'name' => 'Lattafa',
            'slug' => 'lattafa',
        ]);
        $asdaaf = Brand::factory()->create([
            'name' => 'Asdaaf',
            'slug' => 'asdaaf',
        ]);

        $wanted = Product::factory()->create([
            'category_id' => $perfumes->id,
            'brand_id' => $lattafa->id,
            'device_family' => null,
            'case_type' => null,
        ]);
        Product::factory()->create([
            'category_id' => $perfumes->id,
            'brand_id' => $asdaaf->id,
            'device_family' => null,
            'case_type' => null,
        ]);

        $this->get(route('category.show', $perfumes).'?brands[]=lattafa')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('filters.brands', ['lattafa'])
                    ->has('products', 1)
                    ->where('products.0.id', $wanted->id)
                    // Alphabetical, and picking one brand must not drop the
                    // other or there is no way back to the full department.
                    ->has('brands', 2)
                    ->where('brands.0.label', 'Asdaaf')
                    ->where('brands.1.label', 'Lattafa')
                    ->where('brands.1.count', 1)
                    ->has('deviceFamilies', 0)
                    ->has('caseTypes', 0)
            );
    }

    public function test_a_department_carrying_one_brand_is_offered_no_brand_filter(): void
    {
        $protectors = Category::factory()->create(['slug' => 'protektori']);
        $brand = Brand::factory()->create(['slug' => 'etteri']);

        Product::factory()->count(2)->create([
            'category_id' => $protectors->id,
            'brand_id' => $brand->id,
        ]);

        $this->get(route('category.show', $protectors))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->has('brands', 0));
    }

    public function test_an_unknown_brand_slug_is_ignored(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'brand_id' => Brand::factory()->create()->id,
        ]);

        $this->get(route('category.show', $category).'?brands[]=nonsense')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('filters.brands', [])
                    ->has('products', 2)
            );
    }

    public function test_a_facet_group_keeps_its_own_options_but_narrows_the_other(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::IPhone,
            'case_type' => CaseType::Leather,
        ]);
        Product::factory()->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::Samsung,
            'case_type' => CaseType::Silicone,
        ]);

        // Picking iPhone must leave Samsung listed, or a shopper could never
        // widen the selection again. The case types, though, should narrow to
        // what iPhone actually offers.
        $this->get(route('category.show', $category).'?devices[]=iphone')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->has('products', 1)
                    ->has('deviceFamilies', 2)
                    ->where('deviceFamilies.0.value', 'iphone')
                    ->where('deviceFamilies.1.value', 'samsung')
                    ->has('caseTypes', 1)
                    ->where('caseTypes.0.value', 'kozhen')
                    ->where('caseTypes.0.label', 'Кожен')
            );
    }

    public function test_an_unknown_facet_value_is_ignored(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'device_family' => DeviceFamily::IPhone,
        ]);

        $this->get(route('category.show', $category).'?devices[]=nonsense')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('filters.devices', [])
                    ->has('products', 2)
            );
    }

    public function test_a_category_can_be_sorted_by_price(): void
    {
        $category = Category::factory()->create();

        $cheap = Product::factory()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 1000,
        ]);
        $dear = Product::factory()->create([
            'category_id' => $category->id,
            'price_in_stotinki' => 9000,
        ]);

        $this->get(route('category.show', $category).'?sort=price_asc')
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('products.0.id', $cheap->id)
                    ->where('products.1.id', $dear->id)
            );

        $this->get(route('category.show', $category).'?sort=price_desc')
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->where('products.0.id', $dear->id)
                    ->where('products.1.id', $cheap->id)
            );
    }

    public function test_an_unknown_sort_falls_back_to_the_default_order(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->get(route('category.show', $category).'?sort=drop-table')
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page->where(
                    'filters.sort',
                    'default',
                )
            );
    }

    public function test_the_promotions_page_lists_discounted_products_biggest_saving_first(): void
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

        $this->get(route('promotions'))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                    ->component('storefront/Promotions')
                    ->has('products', 2)
                    ->where('products.0.id', $bigSaving->id)
                    ->where('products.1.id', $smallSaving->id)
            );
    }

    public function test_an_unknown_slug_is_a_not_found(): void
    {
        $this->get('/kategoriya/nyama-takava')->assertNotFound();
        $this->get('/produkt/nyama-takav')->assertNotFound();
    }
}
