<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // No auth required for this test task
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'external_id' => 'nullable|integer|unique:products,external_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'thumbnail' => 'nullable|string|url|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'string|url|max:2048',
        ];
    }
}
