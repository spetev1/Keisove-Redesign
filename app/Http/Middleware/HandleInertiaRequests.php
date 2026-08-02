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
     * Each department, plus the handset families it stocks, so the header menu
     * can offer a way straight into a filtered view.
     *
     * @return list<array<string, mixed>>
     */
    protected function storefrontCategories(): array
    {
        $familiesByCategory = $this->deviceFamiliesByCategory();

        return Category::query()
            ->withCount('products')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $category): array => [
                ...(new CategoryResource($category))->resolve(),
                'deviceFamilies' => $familiesByCategory[$category->id] ?? [],
            ])
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
