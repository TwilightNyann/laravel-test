<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product ? $product->id : null;

        return [
            'external_id' => 'sometimes|nullable|integer|unique:products,external_id,'.$productId,
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'brand' => 'sometimes|nullable|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'discount_percentage' => 'sometimes|nullable|numeric|min:0|max:100',
            'rating' => 'sometimes|nullable|numeric|min:0|max:5',
            'stock' => 'sometimes|nullable|integer|min:0',
            'sku' => 'sometimes|nullable|string|max:255|unique:products,sku,'.$productId,
            'thumbnail' => 'sometimes|nullable|string|url|max:2048',
            'images' => 'sometimes|nullable|array',
            'images.*' => 'string|url|max:2048',
        ];
    }
}
