<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private const BATCHES_COUNT = 5;

    private const PRODUCTS_PER_BATCH = 3;

    private const PAUSE_BETWEEN_BATCHES_SECONDS = 2;

    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $categories = Category::query()->get();

        for ($batch = 1; $batch <= self::BATCHES_COUNT; $batch++) {
            Product::factory()
                ->count(self::PRODUCTS_PER_BATCH)
                ->recycle($categories)
                ->create();

            if ($batch < self::BATCHES_COUNT) {
                sleep(self::PAUSE_BETWEEN_BATCHES_SECONDS);
            }
        }
    }
}
