<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Support\Price;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * Everything a product card needs and nothing more - the storefront never
     * receives raw stotinki or timestamps it has no use for.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'imageUrl' => $this->image_path ? asset($this->image_path) : null,
            'price' => Price::format($this->price_in_stotinki),
            'compareAtPrice' => $this->isDiscounted()
                ? Price::format($this->compare_at_price_in_stotinki)
                : null,
            'discountPercentage' => $this->discountPercentage(),
            'isNew' => $this->is_new,
            'categorySlug' => $this->whenLoaded(
                'category',
                fn () => $this->category->slug,
            ),
        ];
    }
}
