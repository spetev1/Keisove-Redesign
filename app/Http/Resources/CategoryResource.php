<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Category
 */
class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'imageUrl' => $this->image_path ? asset($this->image_path) : null,
            'productCount' => $this->whenCounted('products'),
            /*
             * Only where the caller loaded them. A department sends its children
             * so the page can offer them; a child sends its parent so the page
             * can point back up.
             */
            'children' => $this->whenLoaded(
                'children',
                fn () => self::collection($this->children)->resolve(),
            ),
            'parent' => $this->whenLoaded(
                'parent',
                fn () => [
                    'name' => $this->parent->name,
                    'slug' => $this->parent->slug,
                ],
            ),
        ];
    }
}
