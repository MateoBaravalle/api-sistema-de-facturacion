<?php

namespace App\Http\Requests\PaymentRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePaymentRequest extends FormRequest
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
            'status' => ['sometimes', 'string'],
            'proof_file_url' => ['nullable', 'string', 'url'],
            'verified_at' => ['nullable', 'date'],
            'external_transaction_id' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.string' => 'El estado debe ser texto.',
            'proof_file_url.url' => 'La URL del comprobante debe ser válida.',
            'proof_file_url.string' => 'La URL del comprobante debe ser texto.',
            'verified_at.date' => 'La fecha de verificación debe ser una fecha válida.',
            'external_transaction_id.string' => 'El ID de transacción externa debe ser texto.',
        ];
    }
}
