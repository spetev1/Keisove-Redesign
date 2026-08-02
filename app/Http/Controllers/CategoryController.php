<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\CaseType;
use App\Support\DeviceFamily;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /** @var list<string> */
    protected const SORTS = ['default', 'price_asc', 'price_desc', 'newest'];

    /**
     * A group with one option cannot narrow anything, so it is not offered.
     */
    protected const MINIMUM_FACET_OPTIONS = 2;

    public function __invoke(Request $request, Category $category): Response
    {
        $filters = $this->filtersFrom($request);

        return Inertia::render('storefront/Category', [
            'category' => new CategoryResource($category),
            'filters' => $filters,
            'brands' => $this->brandFacets($category, $filters),
            'deviceFamilies' => $this->deviceFacets($category, $filters),
            'caseTypes' => $this->caseTypeFacets($category, $filters),
            'priceBounds' => $this->priceBounds($category),
            'products' => ProductResource::collection(
                $this->results($category, $filters)
            ),
        ]);
    }

    /**
     * Query strings are user input on a public page, so every value is
     * whitelisted or clamped rather than trusted.
     *
     * @return array{q: string|null, brands: list<string>, devices: list<string>, types: list<string>, minPrice: int|null, maxPrice: int|null, onSale: bool, isNew: bool, sort: string}
     */
    protected function filtersFrom(Request $request): array
    {
        $sort = (string) $request->query('sort', 'default');

        return [
            'q' => $this->nullableString($request->query('q')),
            'brands' => $this->brandSlugs($request->query('brands', [])),
            'devices' => $this->enumSlugs(
                $request->query('devices', []),
                DeviceFamily::class,
            ),
            'types' => $this->enumSlugs(
                $request->query('types', []),
                CaseType::class,
            ),
            'minPrice' => $this->nullableInt($request->query('min')),
            'maxPrice' => $this->nullableInt($request->query('max')),
            'onSale' => $request->boolean('sale'),
            'isNew' => $request->boolean('new'),
            'sort' => in_array($sort, self::SORTS, true) ? $sort : 'default',
        ];
    }

    /**
     * Anything that is not a real case of the enum is dropped, so a hand-typed
     * URL can never reach the query.
     *
     * @param  class-string<DeviceFamily|CaseType>  $enum
     * @return list<string>
     */
    protected function enumSlugs(mixed $values, string $enum): array
    {
        $allowed = array_map(fn ($case): string => $case->value, $enum::cases());

        return array_values(array_unique(array_filter(
            (array) $values,
            fn (mixed $value): bool => is_string($value)
                && in_array($value, $allowed, true),
        )));
    }

    /**
     * Brands are rows rather than enum cases, so the whitelist is the set of
     * slugs that actually exist. Anything else is dropped before it reaches a
     * query or is echoed back to the page.
     *
     * @return list<string>
     */
    protected function brandSlugs(mixed $values): array
    {
        $requested = array_values(array_unique(array_filter(
            (array) $values,
            fn (mixed $value): bool => is_string($value) && $value !== '',
        )));

        if ($requested === []) {
            return [];
        }

        return array_values(array_map(
            fn (mixed $slug): string => (string) $slug,
            Brand::query()
                ->whereIn('slug', $requested)
                ->orderBy('name')
                ->pluck('slug')
                ->all(),
        ));
    }

    protected function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    protected function nullableInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        return max(0, (int) $value);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Product>
     */
    protected function results(Category $category, array $filters): Collection
    {
        $query = $this->filtered($category, $filters);

        match ($filters['sort']) {
            'price_asc' => $query->orderBy('products.price_in_stotinki'),
            'price_desc' => $query->orderByDesc('products.price_in_stotinki'),
            'newest' => $query->orderByDesc('products.is_new')
                ->orderByDesc('products.id'),
            default => $query->orderBy('products.sort_order'),
        };

        return $query->with('category')->get();
    }

    /**
     * `$except` leaves one facet's own selection out of the query, so ticking
     * a box never makes its sibling options look empty.
     *
     * @param  array<string, mixed>  $filters
     * @return Builder<Product>
     */
    protected function filtered(
        Category $category,
        array $filters,
        ?string $except = null,
    ): Builder {
        return Product::query()
            ->where('products.category_id', $category->id)
            ->when(
                $filters['q'] !== null,
                fn (Builder $query) => $this->matching($query, $filters['q']),
            )
            ->when(
                $except !== 'brands' && $filters['brands'] !== [],
                fn (Builder $query) => $query->whereHas(
                    'brand',
                    fn (Builder $brand) => $brand->whereIn(
                        'slug',
                        $filters['brands'],
                    ),
                ),
            )
            ->when(
                $except !== 'devices' && $filters['devices'] !== [],
                fn (Builder $query) => $query->whereIn(
                    'products.device_family',
                    $filters['devices'],
                ),
            )
            ->when(
                $except !== 'types' && $filters['types'] !== [],
                fn (Builder $query) => $query->whereIn(
                    'products.case_type',
                    $filters['types'],
                ),
            )
            ->when(
                $filters['minPrice'] !== null,
                fn (Builder $query) => $query->where(
                    'products.price_in_stotinki',
                    '>=',
                    $filters['minPrice'] * 100,
                ),
            )
            ->when(
                $filters['maxPrice'] !== null,
                fn (Builder $query) => $query->where(
                    'products.price_in_stotinki',
                    '<=',
                    $filters['maxPrice'] * 100,
                ),
            )
            ->when(
                $filters['onSale'],
                fn (Builder $query) => $query->whereColumn(
                    'products.compare_at_price_in_stotinki',
                    '>',
                    'products.price_in_stotinki',
                ),
            )
            ->when(
                $filters['isNew'],
                fn (Builder $query) => $query->where('products.is_new', true),
            );
    }

    /**
     * Free text still searches the printed name, since that is where a model
     * number like "S26 Ultra" lives.
     *
     * `whereLike` is used rather than a bare `ilike` because case-insensitive
     * matching is spelled differently per driver, and the tests run on SQLite
     * while the app runs on Postgres.
     *
     * @param  Builder<Product>  $query
     */
    protected function matching(Builder $query, string $filter): void
    {
        $term = '%'.addcslashes($filter, '%_\\').'%';

        $query->where(function (Builder $query) use ($term): void {
            $query->whereLike('products.name', $term, caseSensitive: false)
                ->orWhereLike('products.subtitle', $term, caseSensitive: false);
        });
    }

    /**
     * Brands are the one facet every department can offer, which is what the
     * perfume and accessory pages filter on - neither fits a handset, and
     * neither is built like a case.
     *
     * @param  array<string, mixed>  $filters
     * @return list<array{value: string, label: string, count: int}>
     */
    protected function brandFacets(Category $category, array $filters): array
    {
        if (! $this->offersAChoice($category, 'brand_id')) {
            return [];
        }

        $rows = $this->filtered($category, $filters, except: 'brands')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->selectRaw('brands.slug as value, brands.name as label, count(*) as total')
            ->groupBy('brands.slug', 'brands.name')
            ->orderBy('brands.name')
            ->get();

        return array_values($rows->map(fn (Product $row): array => [
            'value' => (string) $row->getAttribute('value'),
            'label' => (string) $row->getAttribute('label'),
            'count' => (int) $row->getAttribute('total'),
        ])->all());
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{value: string, label: string, count: int}>
     */
    protected function deviceFacets(Category $category, array $filters): array
    {
        if (! $this->offersAChoice($category, 'device_family')) {
            return [];
        }

        return $this->facets(
            DeviceFamily::cases(),
            $this->counts($category, $filters, 'device_family', 'devices'),
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{value: string, label: string, count: int}>
     */
    protected function caseTypeFacets(Category $category, array $filters): array
    {
        if (! $this->offersAChoice($category, 'case_type')) {
            return [];
        }

        return $this->facets(
            CaseType::cases(),
            $this->counts($category, $filters, 'case_type', 'types'),
        );
    }

    /**
     * Judged across the whole department rather than the current selection: a
     * group narrowed to one option by a sibling facet is still worth showing,
     * but a department that only ever had one brand is offered no brand filter
     * at all.
     */
    protected function offersAChoice(Category $category, string $column): bool
    {
        return Product::query()
            ->where('category_id', $category->id)
            ->whereNotNull($column)
            ->distinct()
            ->count($column) >= self::MINIMUM_FACET_OPTIONS;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, int>
     */
    protected function counts(
        Category $category,
        array $filters,
        string $column,
        string $except,
    ): array {
        return $this->filtered($category, $filters, except: $except)
            ->whereNotNull("products.{$column}")
            ->selectRaw("products.{$column} as value, count(*) as total")
            ->groupBy("products.{$column}")
            ->pluck('total', 'value')
            ->map(fn (mixed $total): int => (int) $total)
            ->all();
    }

    /**
     * Facets follow the order the enum declares rather than the alphabet, and
     * anything with nothing behind it is left out entirely.
     *
     * @param  array<int, DeviceFamily|CaseType>  $cases
     * @param  array<string, int>  $counts
     * @return list<array{value: string, label: string, count: int}>
     */
    protected function facets(array $cases, array $counts): array
    {
        $facets = [];

        foreach ($cases as $case) {
            $count = $counts[$case->value] ?? 0;

            if ($count > 0) {
                $facets[] = [
                    'value' => $case->value,
                    'label' => $case->label(),
                    'count' => $count,
                ];
            }
        }

        return $facets;
    }

    /**
     * Whole-leva bounds for the price inputs, taken across the untouched
     * department so the range does not move as filters are applied.
     *
     * @return array{min: int, max: int}
     */
    protected function priceBounds(Category $category): array
    {
        $bounds = Product::query()
            ->where('category_id', $category->id)
            ->selectRaw('MIN(price_in_stotinki) as low, MAX(price_in_stotinki) as high')
            ->first();

        return [
            'min' => (int) floor(((int) $bounds?->getAttribute('low')) / 100),
            'max' => (int) ceil(((int) $bounds?->getAttribute('high')) / 100),
        ];
    }
}
