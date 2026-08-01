<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::whereNotNull('parent_id')->pluck('id')->toArray();

        Product::factory()
            ->count(1000)
            ->state(function () use ($categoryIds) {
                return [
                    'category_id' => fake()->randomElement($categoryIds),
                    'is_featured' => fake()->boolean(25),
                ];
            })
            ->create();
    }
}
