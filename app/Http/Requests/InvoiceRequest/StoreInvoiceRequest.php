<?php

namespace App\Http\Requests\InvoiceRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'in:pending,paid,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es requerido',
            'client_id.exists' => 'El cliente seleccionado no existe',
            'order_id.required' => 'La orden es requerida',
            'order_id.exists' => 'La orden seleccionada no existe',
            'subtotal.required' => 'El subtotal es requerido',
            'subtotal.numeric' => 'El subtotal debe ser un número',
            'discount.numeric' => 'El descuento debe ser un número',
            'total.required' => 'El total es requerido',
            'total.numeric' => 'El total debe ser un número',
            'status.in' => 'El estado debe ser: pending, paid or cancelled',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validación fallida',
            'error' => $validator->errors(),
        ], 422));
    }

    protected function prepareForValidation()
    {
        if (!$this->has('date')) {
            $this->merge([
                'date' => now(),
            ]);
        }

        if (!$this->has('discount')) {
            $this->merge([
                'discount' => 0,
            ]);
        }

        if (!$this->has('status')) {
            $this->merge([
                'status' => 'pending',
            ]);
        }
    }
}
