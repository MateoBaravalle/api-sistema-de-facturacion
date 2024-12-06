<?php

namespace App\Http\Requests\InvoiceRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'order_id' => ['sometimes', 'integer', 'exists:orders,id'],
            'date' => ['sometimes', 'date'],
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'discount' => ['sometimes', 'numeric', 'min:0'],
            'total' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'in:pending,paid,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'El cliente seleccionado no existe',
            'order_id.exists' => 'La orden seleccionada no existe',
            'date.date' => 'El formato de fecha no es válido',
            'subtotal.numeric' => 'El subtotal debe ser un número',
            'discount.numeric' => 'El descuento debe ser un número',
            'total.numeric' => 'El total debe ser un número',
            'status.in' => 'El estado debe ser: pending, paid or cancelled',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'error' => $validator->errors(),
        ], 422));
    }

    protected function prepareForValidation()
    {
        // Recalcular total si se modifica subtotal o descuento
        if (($this->has('subtotal') || $this->has('discount')) && !$this->has('total')) {
            $invoice = $this->route('invoice');
            $subtotal = $this->input('subtotal', $invoice->subtotal);
            $discount = $this->input('discount', $invoice->discount);
            
            $this->merge([
                'total' => $subtotal - $discount,
            ]);
        }
    }
}
