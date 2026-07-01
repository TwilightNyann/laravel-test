<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'title' => $this->title,
            'description' => $this->description,
            'brand' => $this->brand,
            'category' => $this->category,
            'price' => (float) $this->price,
            'discount_percentage' => $this->discount_percentage ? (float) $this->discount_percentage : null,
            'rating' => $this->rating ? (float) $this->rating : null,
            'stock' => (int) $this->stock,
            'sku' => $this->sku,
            'thumbnail' => $this->thumbnail,
            'images' => $this->images,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
