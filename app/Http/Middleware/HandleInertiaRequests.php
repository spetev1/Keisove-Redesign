<?php

namespace App\Http\Middleware;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Product;
use App\Support\DeviceFamily;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',

            /*
             * The storefront header carries a category menu on every page, so
             * the departments are shared rather than repeated by each
             * controller.
             */
            'storefrontCategories' => fn () => $this->storefrontCategories(),
        ];
    }

    /**
     * The departments, each with its children and the handset families it
     * stocks, so the header and footer can offer a way straight into any of
     * them without every controller fetching the taxonomy again.
     *
     * Only departments are returned at the top level; a child is reached
     * through its parent's `children`.
     *
     * @return list<array<string, mixed>>
     */
    protected function storefrontCategories(): array
    {
        $familiesByCategory = $this->deviceFamiliesByCategory();
        $countsByCategory = $this->productCountsByCategory();

        return Category::query()
            ->departments()
            ->with('children')
            ->ordered()
            ->get()
            ->map(fn (Category $department): array => [
                ...(new CategoryResource($department))->resolve(),
                /*
                 * A department holds no products itself, so its count and its
                 * handsets are the sum of whatever sits beneath it.
                 */
                'productCount' => $this->subtreeCount(
                    $department,
                    $countsByCategory,
                ),
                'deviceFamilies' => $this->subtreeFamilies(
                    $department,
                    $familiesByCategory,
                ),
                'children' => $department->children
                    ->map(fn (Category $child): array => [
                        ...(new CategoryResource($child))->resolve(),
                        'productCount' => $countsByCategory[$child->id] ?? 0,
                        'deviceFamilies' => $familiesByCategory[$child->id] ?? [],
                    ])
                    ->all(),
            ])
            ->all();
    }

    /**
     * @param  array<int, int>  $counts
     */
    protected function subtreeCount(Category $department, array $counts): int
    {
        $total = 0;

        foreach ($department->subtreeIds() as $id) {
            $total += $counts[$id] ?? 0;
        }

        return $total;
    }

    /**
     * The handsets anywhere beneath a department, still in the order the enum
     * declares them rather than however the children happened to be visited.
     *
     * @param  array<int, list<array{value: string, label: string}>>  $families
     * @return list<array{value: string, label: string}>
     */
    protected function subtreeFamilies(
        Category $department,
        array $families,
    ): array {
        $stocked = [];

        foreach ($department->subtreeIds() as $id) {
            foreach ($families[$id] ?? [] as $family) {
                $stocked[$family['value']] = $family;
            }
        }

        return array_values(array_filter(
            array_map(
                fn (DeviceFamily $family): ?array => $stocked[$family->value] ?? null,
                DeviceFamily::cases(),
            ),
        ));
    }

    /**
     * Counted in one pass rather than per category, because the taxonomy is now
     * thirty rows and a count per row is thirty queries on every request.
     *
     * @return array<int, int>
     */
    protected function productCountsByCategory(): array
    {
        return Product::query()
            ->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->map(fn (mixed $total): int => (int) $total)
            ->all();
    }

    /**
     * Grouped in one pass rather than per category, and ordered the way the
     * enum declares rather than however the database returns the rows.
     *
     * @return array<int, list<array{value: string, label: string}>>
     */
    protected function deviceFamiliesByCategory(): array
    {
        $present = Product::query()
            ->whereNotNull('device_family')
            ->select('category_id', 'device_family')
            ->distinct()
            ->get()
            ->groupBy('category_id');

        $families = [];

        foreach ($present as $categoryId => $rows) {
            $stocked = $rows
                ->map(fn (Product $row): ?DeviceFamily => $row->device_family)
                ->filter()
                ->all();

            foreach (DeviceFamily::cases() as $family) {
                if (in_array($family, $stocked, true)) {
                    $families[(int) $categoryId][] = [
                        'value' => $family->value,
                        'label' => $family->label(),
                    ];
                }
            }
        }

        return $families;
    }
}
