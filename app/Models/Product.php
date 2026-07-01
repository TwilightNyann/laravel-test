<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'description',
        'brand',
        'category',
        'price',
        'discount_percentage',
        'rating',
        'stock',
        'sku',
        'thumbnail',
        'images',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'discount_percentage' => 'float',
            'rating' => 'float',
            'stock' => 'integer',
            'images' => 'array',
        ];
    }
}
