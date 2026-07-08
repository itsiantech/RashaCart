<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'user_id' => User::factory(),
            'sku' => strtoupper(Str::random(10)),
            'category_id' => null, // Seeder will assign
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(5),
            'short_description' => $this->faker->sentence(),
            'long_description' => $this->faker->paragraph(5),
            'buying_price' => $this->faker->numberBetween(100, 300),
            'price' => $this->faker->numberBetween(400, 800),
            'discount_type' => null,
            'discount_price' => null,
            'sale_price' => 1200,
            'stock' => $this->faker->numberBetween(5, 50),
            'has_variant' => false,
            'thumbnail' => 'demo/products/product.jpg', // demo image

            'status' => true,
            'is_featured' => $this->faker->boolean(40),
        ];
    }
}
