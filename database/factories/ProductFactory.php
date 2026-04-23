<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 5000),
            'category_id' => Category::factory(),
            'in_stock' => fake()->boolean(75),
            'rating' => fake()->randomFloat(1, 0, 5),
        ];
    }
}
