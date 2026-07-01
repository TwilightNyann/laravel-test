<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_list_of_products()
    {
        Product::factory()->count(20)->create();

        $response = $this->getJson('/api/products?limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'external_id', 'title', 'price', 'brand'],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);

        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(20, $response->json('meta.total'));
    }

    public function test_it_filters_products_by_brand()
    {
        Product::factory()->create(['brand' => 'Apple']);
        Product::factory()->create(['brand' => 'APPLE']);
        Product::factory()->create(['brand' => 'Samsung']);

        $response = $this->getJson('/api/products?brand=apple');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
        $this->assertEquals('Apple', $response->json('data.0.brand'));
        $this->assertEquals('APPLE', $response->json('data.1.brand'));
    }

    public function test_it_returns_empty_array_for_non_existent_brand(): void
    {
        Product::factory()->create(['brand' => 'Apple']);

        $response = $this->getJson('/api/products?brand=NonExistentBrand');

        $response->assertStatus(200);
        $this->assertCount(0, $response->json('data'));
    }

    public function test_it_validates_limit_query_parameter_strictly(): void
    {
        Product::factory()->count(5)->create();

        // Valid limit 100 -> ok
        $this->getJson('/api/products?limit=100')->assertStatus(200);

        // Limit 101 -> 422
        $this->getJson('/api/products?limit=101')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);

        // Limit 0 -> 422
        $this->getJson('/api/products?limit=0')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);

        // Limit -5 -> 422
        $this->getJson('/api/products?limit=-5')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);

        // Limit abc -> 422
        $this->getJson('/api/products?limit=abc')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    public function test_page_parameter_changes_returned_records(): void
    {
        Product::factory()->count(10)->create();

        $page1Response = $this->getJson('/api/products?limit=3&page=1');
        $page2Response = $this->getJson('/api/products?limit=3&page=2');

        $page1Response->assertStatus(200);
        $page2Response->assertStatus(200);

        $page1Ids = collect($page1Response->json('data'))->pluck('id')->toArray();
        $page2Ids = collect($page2Response->json('data'))->pluck('id')->toArray();

        $this->assertNotEmpty($page1Ids);
        $this->assertNotEmpty($page2Ids);
        $this->assertEmpty(array_intersect($page1Ids, $page2Ids));
    }
}
