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
            'message' => 'Validation failed',
            'error' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        $clientId = $this->route('client');

        return [
            'name' => 'sometimes|string|max:255',
            'cuit' => 'sometimes|string|unique:clients,cuit,' . $clientId,
            'email' => 'sometimes|email|unique:clients,email,' . $clientId,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
        ];
    }
}
