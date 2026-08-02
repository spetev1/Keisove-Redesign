<?php

namespace App\Models;

use App\Support\CaseType;
use App\Support\DeviceFamily;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $category_id
 * @property int|null $brand_id
 * @property DeviceFamily|null $device_family
 * @property CaseType|null $case_type
 * @property string $name
 * @property string $slug
 * @property string|null $subtitle
 * @property string|null $description
 * @property string|null $image_path
 * @property int $price_in_stotinki
 * @property int|null $compare_at_price_in_stotinki
 * @property bool $is_new
 * @property bool $is_featured
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category $category
 * @property-read Brand|null $brand
 */
#[Fillable([
    'category_id',
    'brand_id',
    'device_family',
    'case_type',
    'name',
    'slug',
    'subtitle',
    'description',
    'image_path',
    'price_in_stotinki',
    'compare_at_price_in_stotinki',
    'is_new',
    'is_featured',
    'sort_order',
])]
#[RouteKey('slug')]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'device_family' => DeviceFamily::class,
            'case_type' => CaseType::class,
            'price_in_stotinki' => 'integer',
            'compare_at_price_in_stotinki' => 'integer',
            'is_new' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * A product is discounted when it carries a struck-through original price
     * that is genuinely higher than what the customer pays today.
     */
    public function isDiscounted(): bool
    {
        return $this->compare_at_price_in_stotinki !== null
            && $this->compare_at_price_in_stotinki > $this->price_in_stotinki;
    }

    /**
     * Whole-percent saving, rounded the way a shopper expects to read it on a
     * badge: 26.90 down to 22.90 shows as -15%, not -14.87%.
     */
    public function discountPercentage(): ?int
    {
        if (! $this->isDiscounted()) {
            return null;
        }

        $saving = $this->compare_at_price_in_stotinki - $this->price_in_stotinki;

        return (int) round($saving / $this->compare_at_price_in_stotinki * 100);
    }

    /**
     * @param  Builder<Product>  $query
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }
}
