<?php

namespace App\Services;

use App\Models\Product;

class ProductSeeder
{
    public function __construct(
        private DummyJsonClient $client
    ) {}

    /**
     * Fetch products and upsert them into the database.
     *
     * @return array{imported: int, updated: int}
     *
     * @throws \Exception
     */
    public function seedSmartphones(): array
    {
        $productsData = $this->client->fetchSmartphones();

        if (empty($productsData)) {
            return ['imported' => 0, 'updated' => 0];
        }

        $records = [];
        foreach ($productsData as $item) {
            $records[] = [
                'external_id' => $item['id'] ?? null,
                'title' => $item['title'] ?? 'Unknown',
                'description' => $item['description'] ?? null,
                'brand' => $item['brand'] ?? null,
                'category' => $item['category'] ?? 'smartphones',
                'price' => $item['price'] ?? 0,
                'discount_percentage' => $item['discountPercentage'] ?? null,
                'rating' => $item['rating'] ?? null,
                'stock' => $item['stock'] ?? 0,
                'sku' => $item['sku'] ?? null,
                'thumbnail' => $item['thumbnail'] ?? null,
                'images' => isset($item['images']) ? json_encode($item['images']) : null,
                // timestamps need to be manually added for upsert
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Count existing records with these external_ids before upsert to estimate "imported" vs "updated"
        $externalIds = array_column($records, 'external_id');
        $existingCount = Product::whereIn('external_id', $externalIds)->count();
        $totalReceived = count($records);

        Product::upsert(
            $records,
            ['external_id'],
            ['title', 'description', 'brand', 'category', 'price', 'discount_percentage', 'rating', 'stock', 'sku', 'thumbnail', 'images', 'updated_at']
        );

        $updated = $existingCount;
        $imported = $totalReceived - $existingCount;

        return [
            'imported' => max(0, $imported),
            'updated' => max(0, $updated),
        ];
    }
}
