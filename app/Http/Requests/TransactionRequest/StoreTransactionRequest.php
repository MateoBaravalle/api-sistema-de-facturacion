<?php

namespace App\Http\Requests\TransactionRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTransactionRequest extends FormRequest
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
            'reference_id' => ['required', 'integer'],
            'reference_type' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:pending,paid,partial_paid,overdue'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'reference_id.required' => 'El ID de referencia es obligatorio',
            'reference_id.integer' => 'El ID de referencia debe ser un número entero',
            'reference_type.required' => 'El tipo de referencia es obligatorio',
            'reference_type.string' => 'El tipo de referencia debe ser texto',
            'description.required' => 'La descripción es obligatoria',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres',
            'amount.required' => 'El monto es obligatorio',
            'amount.numeric' => 'El monto debe ser un valor numérico',
            'amount.min' => 'El monto debe ser mayor o igual a 0',
            'status.required' => 'El estado es obligatorio',
            'status.in' => 'El estado debe ser: pendiente, pagado, pago parcial o vencido',
            'due_date.required' => 'La fecha de vencimiento es obligatoria',
            'due_date.date' => 'La fecha de vencimiento debe ser una fecha válida',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a hoy',
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'pending',
            ]);
        }
    }
}
