<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeedProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_products_from_dummyjson()
    {
        Http::fake([
            'dummyjson.com/products/category/smartphones*' => Http::response([
                'products' => [
                    [
                        'id' => 1,
                        'title' => 'iPhone 9',
                        'description' => 'An apple mobile which is nothing like apple',
                        'price' => 549,
                        'discountPercentage' => 12.96,
                        'rating' => 4.69,
                        'stock' => 94,
                        'brand' => 'Apple',
                        'category' => 'smartphones',
                        'thumbnail' => 'https://dummyjson.com/image/i/products/1/thumbnail.jpg',
                        'images' => [
                            'https://dummyjson.com/image/i/products/1/1.jpg',
                        ],
                    ],
                    [
                        'id' => 2,
                        'title' => 'iPhone X',
                        'description' => 'SIM-Free, Model A19211 6.5-inch Super Retina HD display with OLED technology A12 Bionic chip with ...',
                        'price' => 899,
                        'discountPercentage' => 17.94,
                        'rating' => 4.44,
                        'stock' => 34,
                        'brand' => 'Apple',
                        'category' => 'smartphones',
                        'thumbnail' => 'https://dummyjson.com/image/i/products/2/thumbnail.jpg',
                        'images' => [
                            'https://dummyjson.com/image/i/products/2/1.jpg',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/products/seed');

        $response->assertStatus(200)
            ->assertJson([
                'imported' => 2,
                'updated' => 0,
            ]);

        $this->assertDatabaseCount('products', 2);

        // Second call should update, not duplicate
        $response2 = $this->postJson('/api/products/seed');
        $response2->assertStatus(200)
            ->assertJson([
                'imported' => 0,
                'updated' => 2,
            ]);

        $this->assertDatabaseCount('products', 2);
    }
}
