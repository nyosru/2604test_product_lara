<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    private const CATEGORIES = [
        'Electronics',
        'Books',
        'Home & Kitchen',
        'Sports',
        'Beauty',
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $category) {
            Category::query()->firstOrCreate([
                'name' => $category,
            ]);
        }
    }
}
