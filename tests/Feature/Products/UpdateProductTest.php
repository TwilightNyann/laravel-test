<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_partially_updates_a_product()
    {
        $product = Product::factory()->create([
            'title' => 'Old Title',
            'price' => 100,
        ]);

        $payload = [
            'price' => 200, // Only updating price
        ];

        $response = $this->patchJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.price', 200)
            ->assertJsonPath('data.title', 'Old Title'); // Title remains unchanged

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Old Title',
            'price' => 200,
        ]);
    }

    public function test_it_returns_200_without_changes_on_empty_payload()
    {
        $product = Product::factory()->create([
            'title' => 'Unchanged Title',
            'price' => 100,
        ]);

        $response = $this->patchJson("/api/products/{$product->id}", []);

        $response->assertStatus(200)
            ->assertJsonPath('data.price', 100)
            ->assertJsonPath('data.title', 'Unchanged Title');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Unchanged Title',
            'price' => 100,
        ]);
    }
}
