<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'cuit' => ['sometimes', 'string', 'unique:clients,cuit,' . $clientId],
            'email' => ['sometimes', 'email', 'unique:clients,email,' . $clientId],
            'phone' => ['sometimes', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'address' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string'],
            'province' => ['sometimes', 'string'],
            'credit_limit' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'cuit.string' => 'El CUIT debe ser una cadena de texto.',
            'cuit.unique' => 'El CUIT ya está registrado.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'phone.regex' => 'El teléfono debe contener solo números.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'city.string' => 'La ciudad debe ser una cadena de texto.',
            'province.string' => 'La provincia debe ser una cadena de texto.',
            'credit_limit.numeric' => 'El límite de crédito debe ser un número.',
            'credit_limit.min' => 'El límite de crédito no puede ser negativo.',
        ];
    }
}
