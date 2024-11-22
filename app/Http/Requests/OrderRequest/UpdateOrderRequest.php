<?php

namespace App\Http\Requests\OrderRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'error' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'order_date' => 'sometimes|date',
            'discount' => 'sometimes|numeric|min:0|nullable:false',
            'total_amount' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string',
            'products' => 'sometimes|array|min:1',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'products.*.price' => 'required_with:products|numeric|min:0',
            'products.*.discount' => 'sometimes|numeric|min:0|nullable:false',
        ];
    }
public function messages(): array
{
    return [
        'order_date.date' => 'The order date must be a valid date.',
        'discount.numeric' => 'The discount must be a number.',
        'discount.min' => 'The discount must be at least 0.',
        'discount.nullable' => 'The discount field cannot be null.',
        'total_amount.numeric' => 'The total amount must be a number.',
        'total_amount.min' => 'The total amount must be at least 0.',
        'notes.string' => 'The notes must be a string.',
        'products.array' => 'The products must be an array.',
        'products.min' => 'The products array must have at least one item.',
        'products.*.product_id.required_with' => 'The product ID is required when products are present.',
        'products.*.product_id.exists' => 'The selected product ID does not exist.',
        'products.*.quantity.required_with' => 'The quantity is required when products are present.',
        'products.*.quantity.integer' => 'The quantity must be an integer.',
        'products.*.quantity.min' => 'The quantity must be at least 1.',
        'products.*.price.required_with' => 'The price is required when products are present.',
        'products.*.price.numeric' => 'The price must be a number.',
        'products.*.price.min' => 'The price must be at least 0.',
        'products.*.discount.numeric' => 'The product discount must be a number.',
        'products.*.discount.min' => 'The product discount must be at least 0.',
        'products.*.discount.nullable' => 'The product discount field cannot be null.',
    ];
}
}