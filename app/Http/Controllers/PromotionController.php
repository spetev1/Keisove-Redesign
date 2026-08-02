<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{
    /**
     * Everything currently carrying a struck price, across every department.
     * Biggest saving first, since that is what the page is for.
     */
    public function __invoke(): Response
    {
        return Inertia::render('storefront/Promotions', [
            'products' => ProductResource::collection(
                Product::query()
                    ->whereColumn(
                        'compare_at_price_in_stotinki',
                        '>',
                        'price_in_stotinki',
                    )
                    ->with('category')
                    ->orderByRaw(
                        '(compare_at_price_in_stotinki - price_in_stotinki) DESC'
                    )
                    ->orderBy('sort_order')
                    ->get()
            ),
        ]);
    }
}
