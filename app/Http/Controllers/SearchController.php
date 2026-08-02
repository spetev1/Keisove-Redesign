<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    /**
     * The header search, which unlike the one in the filter panel runs across
     * every department at once.
     */
    public function __invoke(Request $request): Response
    {
        $term = $this->term($request);

        return Inertia::render('storefront/Search', [
            'term' => $term,
            'products' => ProductResource::collection(
                $term === null ? new Collection : $this->results($term)
            ),
        ]);
    }

    /**
     * A blank search is a legitimate way to arrive here - someone trims the
     * query string off the URL - and the page then asks for a term rather than
     * listing the whole catalogue.
     */
    protected function term(Request $request): ?string
    {
        $term = $request->query('q');

        if (! is_string($term)) {
            return null;
        }

        $term = trim($term);

        return $term === '' ? null : $term;
    }

    /**
     * The brand is matched as well as the printed name, so "Lattafa" finds the
     * perfumes even though no product carries that word in its own name.
     *
     * `whereLike` rather than a bare `ilike` because case-insensitive matching
     * is spelled differently per driver, and the tests run on SQLite while the
     * app runs on Postgres. Two things follow from that split, both of which
     * only come right on Postgres: folding the case of a Cyrillic term, and
     * reading the backslash below as an escape rather than as text.
     *
     * @return Collection<int, Product>
     */
    protected function results(string $term): Collection
    {
        $pattern = '%'.addcslashes($term, '%_\\').'%';

        return Product::query()
            ->where(function (Builder $query) use ($pattern): void {
                $query->whereLike('products.name', $pattern, caseSensitive: false)
                    ->orWhereLike('products.subtitle', $pattern, caseSensitive: false)
                    ->orWhereHas(
                        'brand',
                        fn (Builder $brand) => $brand->whereLike(
                            'brands.name',
                            $pattern,
                            caseSensitive: false,
                        ),
                    );
            })
            ->with(['category', 'brand'])
            // Grouped by department, then in the order the department lists
            // them, so results read the way the rest of the store does.
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get();
    }
}
