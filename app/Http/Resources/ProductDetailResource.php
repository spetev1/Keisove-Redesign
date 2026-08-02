<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * The card shape plus the copy only the product page has room to show.
 *
 * @mixin Product
 */
class ProductDetailResource extends ProductResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'description' => $this->description,
            'brandName' => $this->whenLoaded(
                'brand',
                fn () => $this->brand?->name,
            ),
            'categoryName' => $this->whenLoaded(
                'category',
                fn () => $this->category->name,
            ),
        ];
    }
}
