<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Support\DeviceFamily;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * How many products each homepage row loads. Four or five stand across a
     * full-width row; the rest are what it scrolls to.
     */
    protected const PRODUCTS_PER_ROW = 10;

    public function __invoke(): Response
    {
        return Inertia::render('storefront/Home', [
            'categories' => CategoryResource::collection(
                Category::orderBy('sort_order')->get()
            ),
            'deviceFamilies' => $this->deviceFamilies(),
            'promotions' => ProductResource::collection($this->discounted()),
            'newProducts' => ProductResource::collection($this->arrivals()),
            'accessories' => ProductResource::collection(
                $this->fromDepartment('aksesoari')
            ),
        ]);
    }

    /**
     * The handsets the brand strip runs past its label. Taken from the enum the
     * category filters are built from, so the homepage cannot end up naming a
     * handset the filters do not offer.
     *
     * @return list<array{value: string, label: string}>
     */
    protected function deviceFamilies(): array
    {
        return array_map(
            fn (DeviceFamily $family): array => [
                'value' => $family->value,
                'label' => $family->label(),
            ],
            DeviceFamily::cases(),
        );
    }

    /**
     * Everything carrying a struck price, biggest saving first - the same order
     * the promotions page itself lists them in, so the row reads as the front of
     * that page rather than a different selection.
     *
     * @return Collection<int, Product>
     */
    protected function discounted(): Collection
    {
        return $this->row()
            ->whereColumn(
                'compare_at_price_in_stotinki',
                '>',
                'price_in_stotinki',
            )
            ->orderByRaw(
                '(compare_at_price_in_stotinki - price_in_stotinki) DESC'
            )
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    protected function arrivals(): Collection
    {
        return $this->row()
            ->where('is_new', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return Collection<int, Product>
     */
    protected function fromDepartment(string $categorySlug): Collection
    {
        return $this->row()
            ->whereRelation('category', 'slug', $categorySlug)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * What every row on the page has in common: its department for the card's
     * label, and a ceiling on how much of the catalogue it pulls.
     *
     * @return Builder<Product>
     */
    protected function row(): Builder
    {
        return Product::query()
            ->with('category')
            ->limit(self::PRODUCTS_PER_ROW);
    }
}
