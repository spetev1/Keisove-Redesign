<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $tagline
 * @property string|null $image_path
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Category|null $parent
 * @property-read Collection<int, Category> $children
 * @property-read Collection<int, Product> $products
 */
#[Fillable(['parent_id', 'name', 'slug', 'tagline', 'image_path', 'sort_order'])]
#[RouteKey('slug')]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
        ];
    }

    /**
     * Products hang off a leaf rather than a department, so a department's own
     * relation is usually empty. `productsInSubtree` is what a page should
     * count and list from.
     *
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * @return HasMany<Category, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function isDepartment(): bool
    {
        return $this->parent_id === null;
    }

    /**
     * The categories a page should draw products from: this one, plus its
     * children where it has any.
     *
     * The taxonomy is deliberately one level deep, so this is a single query
     * rather than a recursive walk. Nesting it further would want a recursive
     * CTE, which is the point at which the shape should be reconsidered.
     *
     * @return list<int>
     */
    public function subtreeIds(): array
    {
        if (! $this->isDepartment()) {
            return [$this->id];
        }

        return [
            $this->id,
            ...$this->children()->pluck('id')->all(),
        ];
    }

    /**
     * Everything beneath this category, however deep the page is standing.
     * A department gathers its children's products; a child answers for its
     * own.
     *
     * @param  Builder<Category>  $query
     */
    #[Scope]
    protected function departments(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    /**
     * @param  Builder<Category>  $query
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
