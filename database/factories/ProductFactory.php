<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'external_id' => fake()->unique()->randomNumber(5),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'brand' => fake()->randomElement(['Apple', 'Samsung', 'Google', 'OnePlus']),
            'category' => 'smartphones',
            'price' => fake()->randomFloat(2, 100, 1500),
            'discount_percentage' => fake()->randomFloat(2, 0, 20),
            'rating' => fake()->randomFloat(2, 1, 5),
            'stock' => fake()->numberBetween(0, 100),
            'sku' => fake()->unique()->ean8(),
            'thumbnail' => fake()->imageUrl(),
            'images' => [fake()->imageUrl(), fake()->imageUrl()],
        ];
    }
}
