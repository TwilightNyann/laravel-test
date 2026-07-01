<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class DummyJsonClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = (string) config('services.dummyjson.url', 'https://dummyjson.com');
    }

    /**
     * Fetch all smartphone products from DummyJSON API.
     *
     * @return array<int, mixed>
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function fetchSmartphones(): array
    {
        $response = Http::timeout(15)->retry(2, 200)->get("{$this->baseUrl}/products/category/smartphones", [
            'limit' => 0,
        ]);

        $response->throw();

        return $response->json('products', []);
    }
}
