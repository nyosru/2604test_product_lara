<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_products(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/api/products?per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_it_filters_products_by_query_price_category_stock_and_rating(): void
    {
        $phones = Category::factory()->create();
        $books = Category::factory()->create();

        $match = Product::factory()->create([
            'name' => 'Apple iPhone 15',
            'price' => 999.99,
            'category_id' => $phones->id,
            'in_stock' => true,
            'rating' => 4.8,
        ]);

        Product::factory()->create([
            'name' => 'Apple MacBook',
            'price' => 1999.99,
            'category_id' => $phones->id,
            'in_stock' => true,
            'rating' => 4.9,
        ]);

        Product::factory()->create([
            'name' => 'iPhone User Guide',
            'price' => 19.99,
            'category_id' => $books->id,
            'in_stock' => false,
            'rating' => 4.2,
        ]);

        $this->getJson("/api/products?q=iPhone&price_from=100&price_to=1200&category_id={$phones->id}&in_stock=true&rating_from=4.5")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_it_filters_products_by_price_to_without_price_from(): void
    {
        Product::factory()->create([
            'name' => 'Cheap product',
            'price' => 50,
        ]);
        Product::factory()->create([
            'name' => 'Boundary product',
            'price' => 100,
        ]);
        Product::factory()->create([
            'name' => 'Expensive product',
            'price' => 150,
        ]);

        $this->getJson('/api/products?price_to=100&sort=price_asc')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Cheap product')
            ->assertJsonPath('data.1.name', 'Boundary product');
    }

    public function test_it_sorts_products_by_price_rating_and_newest(): void
    {
        Product::factory()->create([
            'name' => 'Old expensive',
            'price' => 300,
            'rating' => 3.5,
            'created_at' => now()->subDay(),
        ]);
        Product::factory()->create([
            'name' => 'Newest cheap',
            'price' => 100,
            'rating' => 4.0,
            'created_at' => now(),
        ]);
        Product::factory()->create([
            'name' => 'Best rated',
            'price' => 200,
            'rating' => 4.9,
            'created_at' => now()->subHour(),
        ]);

        $this->getJson('/api/products?sort=price_asc')
            ->assertJsonPath('data.0.name', 'Newest cheap');

        $this->getJson('/api/products?sort=price_desc')
            ->assertJsonPath('data.0.name', 'Old expensive');

        $this->getJson('/api/products?sort=rating_desc')
            ->assertJsonPath('data.0.name', 'Best rated');

        $this->getJson('/api/products?sort=newest')
            ->assertJsonPath('data.0.name', 'Newest cheap');
    }

    public function test_it_validates_query_parameters(): void
    {
        $this->getJson('/api/products?sort=wrong&rating_from=6&per_page=101')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sort', 'rating_from', 'per_page']);
    }
}
