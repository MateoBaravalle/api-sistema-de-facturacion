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
            'order_date' => ['sometimes', 'date'],
            'discount' => ['sometimes', 'numeric', 'min:0'],
            'total_amount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'products' => ['sometimes', 'array', 'min:1'],
            'products.*.product_id' => ['required_with:products', 'exists:products,id'],
            'products.*.quantity' => ['required_with:products', 'integer', 'min:1'],
            'products.*.price' => ['required_with:products', 'numeric', 'min:0'],
            'products.*.discount' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_date.date' => 'La fecha de la orden debe ser una fecha válida.',
            'discount.numeric' => 'El descuento debe ser un número.',
            'discount.min' => 'El descuento debe ser al menos 0.',
            'discount.nullable' => 'El campo de descuento no puede ser nulo.',
            'total_amount.numeric' => 'El total debe ser un número.',
            'total_amount.min' => 'El total debe ser al menos 0.',
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'products.array' => 'Los productos deben ser un array.',
            'products.min' => 'El array de productos debe tener al menos un elemento.',
            'products.*.product_id.required_with' => 'El ID del producto es requerido cuando hay productos.',
            'products.*.product_id.exists' => 'El ID del producto seleccionado no existe.',
            'products.*.quantity.required_with' => 'La cantidad es requerida cuando hay productos.',
            'products.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad debe ser al menos 1.',
            'products.*.price.required_with' => 'El precio es requerido cuando hay productos.',
            'products.*.price.numeric' => 'El precio debe ser un número.',
            'products.*.price.min' => 'El precio debe ser al menos 0.',
            'products.*.discount.numeric' => 'El descuento del producto debe ser un número.',
            'products.*.discount.min' => 'El descuento del producto debe ser al menos 0.',
        ];
    }
}
