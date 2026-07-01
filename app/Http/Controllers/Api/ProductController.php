<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListProductsRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductSeeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListProductsRequest $request): AnonymousResourceCollection
    {
        $limit = $request->input('limit', 15);

        $query = Product::query();

        if ($request->filled('brand')) {
            $brand = $request->input('brand');
            $query->whereRaw('LOWER(brand) = ?', [strtolower($brand)]);
        }

        $products = $query->paginate($limit);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * Fetch products from DummyJSON API and seed the database.
     */
    public function seed(ProductSeeder $seeder)
    {
        try {
            $result = $seeder->seedSmartphones();

            return response()->json($result);
        } catch (ConnectionException|RequestException $e) {
            return response()->json([
                'message' => 'Bad Gateway: Cannot connect to external API',
            ], 502);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
            ], 500);
        }
    }
}
