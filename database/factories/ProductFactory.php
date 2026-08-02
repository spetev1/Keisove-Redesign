<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'subtitle' => fake()->words(2, true),
            'description' => fake()->paragraph(),
            'image_path' => null,
            'price_in_stotinki' => fake()->numberBetween(990, 12990),
            'compare_at_price_in_stotinki' => null,
            'is_new' => false,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    /**
     * Carry a struck-through original price, so the card renders a discount badge.
     */
    public function discounted(int $percentage = 15): static
    {
        return $this->state(fn (array $attributes) => [
            'compare_at_price_in_stotinki' => (int) round(
                $attributes['price_in_stotinki'] / (1 - $percentage / 100)
            ),
        ]);
    }

    public function isNew(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_new' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
