<?php

namespace App\Http\Requests\OrderRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
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
            'reference_type' => 'required|string|in:client,supplier',
            'reference_id' => 'required|integer',
            'order_date' => 'required|date',
            'discount' => 'sometimes|numeric|min:0|nullable:false',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.discount' => 'sometimes|numeric|min:0|nullable:false',
        ];
    }

    public function messages(): array
    {
        return [
            'reference_type.required' => 'El tipo de referencia es obligatorio.',
            'reference_type.string' => 'El tipo de referencia debe ser una cadena de texto.',
            'reference_type.in' => 'El tipo de referencia debe ser uno de los siguientes valores: client, supplier.',
            'reference_id.required' => 'El ID de referencia es obligatorio.',
            'reference_id.integer' => 'El ID de referencia debe ser un número entero.',
            'order_date.required' => 'La fecha del pedido es obligatoria.',
            'order_date.date' => 'La fecha del pedido debe ser una fecha válida.',
            'discount.sometimes' => 'El descuento es opcional.',
            'discount.numeric' => 'El descuento debe ser un número.',
            'discount.min' => 'El descuento no puede ser negativo.',
            'discount.nullable' => 'El descuento no puede ser nulo.',
            'total_amount.required' => 'El monto total es obligatorio.',
            'total_amount.numeric' => 'El monto total debe ser un número.',
            'total_amount.min' => 'El monto total no puede ser negativo.',
            'notes.nullable' => 'Las notas son opcionales.',
            'notes.string' => 'Las notas deben ser una cadena de texto.',
            'products.required' => 'Los productos son obligatorios.',
            'products.array' => 'Los productos deben ser un arreglo.',
            'products.min' => 'Debe haber al menos un producto.',
            'products.*.product_id.required' => 'El ID del producto es obligatorio.',
            'products.*.product_id.exists' => 'El ID del producto debe existir en la base de datos.',
            'products.*.quantity.required' => 'La cantidad del producto es obligatoria.',
            'products.*.quantity.integer' => 'La cantidad del producto debe ser un número entero.',
            'products.*.quantity.min' => 'La cantidad del producto debe ser al menos 1.',
            'products.*.price.required' => 'El precio del producto es obligatorio.',
            'products.*.price.numeric' => 'El precio del producto debe ser un número.',
            'products.*.price.min' => 'El precio del producto no puede ser negativo.',
            'products.*.discount.sometimes' => 'El descuento del producto es opcional.',
            'products.*.discount.numeric' => 'El descuento del producto debe ser un número.',
            'products.*.discount.min' => 'El descuento del producto no puede ser negativo.',
            'products.*.discount.nullable' => 'El descuento del producto no puede ser nulo.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => 'pending',
        ]);

        if (!$this->has('discount')) {
            $this->merge([
                'discount' => 0,
            ]);
        }

        if ($this->has('products')) {
            $products = $this->get('products');
            foreach ($products as &$product) {
                if (!isset($product['discount'])) {
                    $product['discount'] = 0;
                }
            }
            $this->merge(['products' => $products]);
        }
    }
}
