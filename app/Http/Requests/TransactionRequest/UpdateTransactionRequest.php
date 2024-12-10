<?php

namespace App\Http\Requests\TransactionRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTransactionRequest extends FormRequest
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
            'description' => ['sometimes', 'string', 'max:1000'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'string', 'in:pending,paid,partial_paid,overdue'],
            'due_date' => ['sometimes', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.string' => 'La descripción debe ser texto',
            'description.max' => 'La descripción no puede exceder los 1000 caracteres',
            'amount.numeric' => 'El monto debe ser un valor numérico',
            'amount.min' => 'El monto debe ser mayor o igual a 0',
            'status.in' => 'El estado debe ser: pendiente, pagado, pago parcial o vencido',
            'due_date.date' => 'La fecha de vencimiento debe ser una fecha válida',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a hoy',
        ];
    }
}
