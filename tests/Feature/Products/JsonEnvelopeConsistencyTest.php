<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JsonEnvelopeConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_same_json_envelope_shape_across_all_product_endpoints(): void
    {
        $payload = [
            'external_id' => 1001,
            'title' => 'Test Envelope Smartphone',
            'category' => 'smartphones',
            'price' => 599.99,
            'brand' => 'TestBrand',
            'stock' => 15,
            'sku' => 'SKU-ENV-1001',
        ];

        // 1. Create -> 201
        $createResponse = $this->postJson('/api/products', $payload);
        $createResponse->assertStatus(201);
        $createData = $createResponse->json('data');
        $this->assertIsArray($createData);
        $this->assertEquals('Test Envelope Smartphone', $createData['title']);

        $productId = $createData['id'];

        // 2. Show -> 200
        $showResponse = $this->getJson("/api/products/{$productId}");
        $showResponse->assertStatus(200);
        $showData = $showResponse->json('data');
        $this->assertIsArray($showData);

        // 3. Update -> 200
        $updateResponse = $this->patchJson("/api/products/{$productId}", ['price' => 549.99]);
        $updateResponse->assertStatus(200);
        $updateData = $updateResponse->json('data');
        $this->assertIsArray($updateData);

        // 4. Verify exact key sets match across create, show, and update envelopes
        $this->assertEquals(array_keys($createData), array_keys($showData));
        $this->assertEquals(array_keys($showData), array_keys($updateData));
    }
}
