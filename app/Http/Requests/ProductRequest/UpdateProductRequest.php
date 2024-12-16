<?php

namespace App\Http\Requests\ProductRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validación fallida',
            'error' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['sometimes', 'exists:suppliers,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'purchase_price' => ['sometimes', 'numeric', 'min:0'],
            'sale_price' => ['sometimes', 'numeric', 'min:0', 'gte:purchase_price'],
            'category' => ['nullable', 'string', 'max:255'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'string', 'in:available,discontinued'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.exists' => 'El proveedor seleccionado no existe.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'purchase_price.numeric' => 'El precio de compra debe ser numérico.',
            'purchase_price.min' => 'El precio de compra no puede ser negativo.',
            'sale_price.numeric' => 'El precio de venta debe ser numérico.',
            'sale_price.min' => 'El precio de venta no puede ser negativo.',
            'sale_price.gte' => 'El precio de venta debe ser mayor o igual al precio de compra.',
            'category.string' => 'La categoría debe ser texto.',
            'category.max' => 'La categoría no puede exceder 255 caracteres.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'status.string' => 'El estado debe ser texto.',
            'status.in' => 'El estado debe ser available o discontinued.',
        ];
    }
}
