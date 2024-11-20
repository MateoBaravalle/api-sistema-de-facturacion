<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'cuit' => 'required|string|unique:clients,cuit',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'credit_limit' => 'sometimes|numeric|min:0',
            'balance' => 'sometimes|numeric',
            'status' => 'sometimes|in:positive,current,negative',
        ];
    }

    protected function prepareForValidation()
    {
        // Establecer status por defecto si no existe
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'current',
            ]);
        }

        // Establecer balance inicial en 0 si no existe
        if (!$this->has('balance')) {
            $this->merge([
                'balance' => 0,
            ]);
        }

        // Establecer credit_limit inicial en 0 si no existe
        if (!$this->has('credit_limit')) {
            $this->merge([
                'credit_limit' => 0,
            ]);
        }
    }
}
