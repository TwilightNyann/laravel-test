<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.title', $product->title);
    }

    public function test_it_returns_404_for_non_existent_product()
    {
        $response = $this->getJson('/api/products/9999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }
}
