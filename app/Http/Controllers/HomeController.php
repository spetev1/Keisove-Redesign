<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Support\DeviceFamily;
use App\Support\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * How many products the "Нови продукти" grid loads. Eight fills the grid to
     * two full rows at desktop widths, which is what the design lays out.
     */
    protected const NEW_PRODUCTS_LIMIT = 8;

    /**
     * How many photographs the hero's collage runs behind the copy. Two columns
     * of three, each column looping its own set.
     */
    protected const COLLAGE_SIZE = 6;

    /**
     * The department the hero borrows its collage and its handset card from -
     * the redesign leads on cases, and the copy beside the collage is about
     * finding one for your phone.
     *
     * The whole department, so "find one for your phone" reaches silicone,
     * leather and hard backs alike rather than one shelf of them.
     */
    protected const LEAD_DEPARTMENT = 'kalafi';

    /** The department the bestseller card opens. */
    protected const BESTSELLER_DEPARTMENT = 'parfyumi';

    /**
     * The tiles the homepage grid lays out, in the design's order.
     *
     * This is not the nav row's order and it is not the taxonomy's. The design
     * carries two different sequences on purpose: the row names six departments,
     * while the grid mixes levels - departments where the department is the
     * point, subcategories where the division is - and orders them so the
     * perfumes fall at the start of the second row, which is where the violet
     * tile is drawn.
     *
     * Which categories, and in what order, is an editorial choice like the lead
     * and bestseller departments above it. The rows themselves still come from
     * the database, counts and artwork included.
     *
     * @var list<string>
     */
    protected const HOME_GRID = [
        'silikonovi-garbove',
        'kozheni-kalafi',
        'protektori',
        'zaryadni',
        'bluetooth-slushalki',
        'power-bank',
        'smart-chasovnitsi',
        // Eighth, so it opens the second row: bottom left, as the design has it.
        'parfyumi',
        'tvardi-garbove',
        'usb-kabeli',
        'stoyki-za-kola',
        'kalafi-za-tableti',
    ];

    /**
     * Departments are shared from HandleInertiaRequests with their counts
     * already on them, so the page does not ask for them again here.
     */
    public function __invoke(): Response
    {
        return Inertia::render('storefront/Home', [
            'featuredCategories' => $this->featuredCategories(),
            'deviceFamilies' => $this->deviceFamilies(),
            'newProducts' => ProductResource::collection($this->newArrivals()),
            'heroCollage' => $this->heroCollage(),
            'spotlights' => [
                'newArrivals' => $this->newArrivalsSpotlight(),
                'bestseller' => $this->bestsellerSpotlight(),
            ],
            'saleEndsAt' => $this->saleEndsAt()->toIso8601String(),
        ]);
    }

    /**
     * The grid's tiles, resolved from the slugs above and kept in that order.
     *
     * A tile's count is everything beneath it, so the "Калъфи" tiles count their
     * own shelf while a department like "Зарядни" counts all four of its. A slug
     * that names nothing is skipped rather than rendered as an empty tile, so a
     * renamed category loses its place in the grid instead of breaking the page.
     *
     * @return list<array<string, mixed>>
     */
    protected function featuredCategories(): array
    {
        $categories = Category::query()
            ->whereIn('slug', self::HOME_GRID)
            ->with('children')
            ->get()
            ->keyBy('slug');

        $tiles = [];

        foreach (self::HOME_GRID as $slug) {
            $category = $categories->get($slug);

            if ($category === null) {
                continue;
            }

            $tiles[] = [
                ...(new CategoryResource($category))->resolve(),
                'productCount' => Product::whereIn(
                    'category_id',
                    $category->subtreeIds(),
                )->count(),
                // The grid links to the tile, not to its children, so they are
                // not sent - the department's own page is what offers them.
                'children' => [],
            ];
        }

        return $tiles;
    }

    /**
     * The handsets the brand panel offers. Taken from the enum the category
     * filters are built from, so the homepage cannot end up naming a handset
     * the filters do not offer.
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
     * What the grid shows: the newest arrivals, one department at a time.
     *
     * Newest-first alone is not enough. The catalogue is seeded department by
     * department, so whichever went in last owns the highest ids and fills all
     * eight slots - a phone accessory shop whose new arrivals are eight toys.
     * Dealing round-robin across the departments instead means the row opens on
     * a spread of the shop, and the filter pills beside it have something to
     * filter.
     *
     * @return Collection<int, Product>
     */
    protected function newArrivals(): Collection
    {
        $candidates = Product::query()
            ->with('category')
            ->where('is_new', true)
            ->orderByDesc('id')
            ->get();

        return $this->oneDepartmentAtATime($candidates)
            ->take(self::NEW_PRODUCTS_LIMIT);
    }

    /**
     * Re-orders products so consecutive ones come from different departments,
     * keeping each department's own newest-first order within its turn.
     *
     * @param  Collection<int, Product>  $products
     * @return Collection<int, Product>
     */
    protected function oneDepartmentAtATime(Collection $products): Collection
    {
        /*
         * Products hang off a leaf, so what groups them is the leaf's parent
         * where it has one - a charger and a cable are the same department even
         * though they are different categories.
         */
        $byDepartment = $products
            ->groupBy(fn (Product $product): int => $product->category->parent_id
                ?? $product->category->id)
            ->values();

        $dealt = collect();
        $round = 0;
        $deepest = $byDepartment->max(fn (Collection $group): int => $group->count()) ?? 0;

        while ($round < $deepest) {
            foreach ($byDepartment as $group) {
                if ($group->has($round)) {
                    $dealt->push($group[$round]);
                }
            }

            $round++;
        }

        return $dealt;
    }

    /**
     * The photographs behind the hero copy. Cases first, because that is what
     * the collage is meant to be a wall of, and topped up from the rest of the
     * catalogue if the department is short - a column with a gap in it would
     * show the violet through the middle of the loop.
     *
     * @return list<string>
     */
    protected function heroCollage(): array
    {
        $lead = $this->subtreeOf(self::LEAD_DEPARTMENT);

        $cases = $this->photographed()
            ->whereIn('category_id', $lead)
            ->orderBy('sort_order')
            ->limit(self::COLLAGE_SIZE)
            ->pluck('image_path');

        if ($cases->count() < self::COLLAGE_SIZE) {
            $cases = $cases->concat(
                $this->photographed()
                    ->whereNotIn('category_id', $lead)
                    ->orderBy('sort_order')
                    ->limit(self::COLLAGE_SIZE - $cases->count())
                    ->pluck('image_path')
            );
        }

        return $cases
            ->map(fn (string $path): string => asset($path))
            ->values()
            ->all();
    }

    /**
     * The handset card beside the hero. It names whichever family the store has
     * added the most new cases for, so the card is pointing at something that
     * genuinely just landed rather than at a fixed model.
     *
     * @return array{subject: string, href: string, imageUrl: string|null}
     */
    protected function newArrivalsSpotlight(): array
    {
        $lead = $this->subtreeOf(self::LEAD_DEPARTMENT);

        $product = $this->photographed()
            ->whereIn('category_id', $lead)
            ->whereNotNull('device_family')
            ->where('is_new', true)
            ->orderByDesc('id')
            ->first();

        /*
         * Nothing new in the department is a legitimate state - it just means
         * the card falls back to the handset with the deepest range instead.
         */
        $product ??= $this->photographed()
            ->whereIn('category_id', $lead)
            ->whereNotNull('device_family')
            ->orderBy('sort_order')
            ->first();

        $family = $product?->device_family;

        return [
            'subject' => $family?->label() ?? '',
            'href' => route('category.show', [
                'category' => self::LEAD_DEPARTMENT,
                ...($family !== null ? ['devices' => [$family->value]] : []),
            ]),
            'imageUrl' => $product?->image_path
                ? asset($product->image_path)
                : null,
        ];
    }

    /**
     * The perfume card. Its price is the department's genuine cheapest bottle
     * rather than a figure written into the design, so it cannot promise an
     * entry price the catalogue does not have.
     *
     * @return array{subject: string, href: string, imageUrl: string|null, fromPrice: string|null}
     */
    protected function bestsellerSpotlight(): array
    {
        $department = Category::query()
            ->where('slug', self::BESTSELLER_DEPARTMENT)
            ->first();

        $product = $this->photographed()
            ->whereIn(
                'category_id',
                $this->subtreeOf(self::BESTSELLER_DEPARTMENT),
            )
            ->orderBy('price_in_stotinki')
            ->first();

        return [
            'subject' => $department?->name ?? '',
            'href' => route('category.show', [
                'category' => self::BESTSELLER_DEPARTMENT,
            ]),
            'imageUrl' => $product?->image_path
                ? asset($product->image_path)
                : null,
            'fromPrice' => $product !== null
                ? Price::format($product->price_in_stotinki)
                : null,
        ];
    }

    /**
     * When the promotion the countdown band runs to expires.
     *
     * A demo link is opened over weeks rather than on one afternoon, so with no
     * date configured the band runs to the end of the current week instead of a
     * fixed moment that would be in the past by the second viewing. Setting
     * `DEMO_SALE_ENDS_AT` pins it for a scheduled pitch.
     */
    protected function saleEndsAt(): Carbon
    {
        $configured = config('demo.sale_ends_at');

        if (filled($configured)) {
            return Carbon::parse((string) $configured);
        }

        return Carbon::now()->endOfWeek();
    }

    /**
     * Anything the collage or a spotlight card can actually show. A product
     * with no photograph would leave a hole where the design has a picture.
     *
     * @return Builder<Product>
     */
    protected function photographed(): Builder
    {
        return Product::query()->whereNotNull('image_path');
    }

    /**
     * The categories a department covers - itself and its children. Products
     * hang off the children, so naming the department alone would match
     * nothing.
     *
     * @return list<int>
     */
    protected function subtreeOf(string $departmentSlug): array
    {
        $department = Category::query()
            ->where('slug', $departmentSlug)
            ->first();

        return $department?->subtreeIds() ?? [];
    }
}
