<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_product_with_valid_data()
    {
        $payload = [
            'title' => 'iPhone 15',
            'category' => 'smartphones',
            'price' => 999.99,
            'brand' => 'Apple',
            'stock' => 10,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'iPhone 15')
            ->assertJsonPath('data.price', 999.99);

        $this->assertDatabaseHas('products', [
            'title' => 'iPhone 15',
            'price' => 999.99,
        ]);
    }

    public function test_it_returns_422_for_invalid_data()
    {
        $payload = [
            'title' => '', // invalid: required
            'price' => -10, // invalid: min 0
            // missing category
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['title', 'price', 'category']]);
    }
}
