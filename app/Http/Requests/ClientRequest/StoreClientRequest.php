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
            'phone' => 'required|string|max:20|regex:/^[0-9]+$/',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'credit_limit' => 'sometimes|numeric|min:0',
            'balance' => 'sometimes|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'cuit.required' => 'El CUIT es obligatorio.',
            'cuit.string' => 'El CUIT debe ser una cadena de texto.',
            'cuit.unique' => 'El CUIT ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'phone.regex' => 'El teléfono debe contener solo números.',
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'city.required' => 'La ciudad es obligatoria.',
            'city.string' => 'La ciudad debe ser una cadena de texto.',
            'province.required' => 'La provincia es obligatoria.',
            'province.string' => 'La provincia debe ser una cadena de texto.',
            'credit_limit.numeric' => 'El límite de crédito debe ser un número.',
            'credit_limit.min' => 'El límite de crédito no puede ser negativo.',
            'balance.numeric' => 'El balance debe ser un número.',
        ];
    }

    protected function prepareForValidation()
    {
        // Establecer balance inicial en 0 si no existe
        if (!$this->has('balance')) {
            $this->merge([
                'balance' => 0,
            ]);
        }

        // Establecer status basado en el valor de balance
        if (!$this->has('status')) {
            $balance = $this->input('balance');
            $status = 'current';

            if ($balance > 0) {
                $status = 'positive';
            } elseif ($balance < 0) {
                $status = 'negative';
            }

            $this->merge([
                'status' => $status,
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
