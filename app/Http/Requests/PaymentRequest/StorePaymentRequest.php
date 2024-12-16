<?php

namespace App\Http\Requests\PaymentRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePaymentRequest extends FormRequest
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
            'transaction_id' => ['required', 'exists:transactions,id'],
            'payment_method' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'proof_file_url' => ['nullable', 'string', 'url'],
            'external_transaction_id' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_id.required' => 'La transacción es obligatoria.',
            'transaction_id.exists' => 'La transacción seleccionada no existe.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.string' => 'El método de pago debe ser texto.',
            'amount.required' => 'El monto es obligatorio.',
            'amount.numeric' => 'El monto debe ser numérico.',
            'amount.min' => 'El monto no puede ser negativo.',
            'payment_date.required' => 'La fecha de pago es obligatoria.',
            'payment_date.date' => 'La fecha de pago debe ser una fecha válida.',
            'proof_file_url.url' => 'La URL del comprobante debe ser válida.',
            'proof_file_url.string' => 'La URL del comprobante debe ser texto.',
            'external_transaction_id.string' => 'El ID de transacción externa debe ser texto.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => 'pending',
            'verified_at' => null,
            'reference_number' => uniqid('PAY-'),
        ]);
    }
}
