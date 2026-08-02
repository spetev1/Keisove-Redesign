<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * How many stablemates the product page suggests under the detail.
     */
    protected const RELATED_PRODUCTS = 8;

    public function __invoke(Product $product): Response
    {
        $product->load(['category', 'brand']);

        return Inertia::render('storefront/Product', [
            'product' => new ProductDetailResource($product),
            'relatedProducts' => ProductResource::collection(
                Product::query()
                    ->where('category_id', $product->category_id)
                    ->whereKeyNot($product->getKey())
                    ->with('category')
                    ->orderBy('sort_order')
                    ->limit(self::RELATED_PRODUCTS)
                    ->get()
            ),
        ]);
    }
}
