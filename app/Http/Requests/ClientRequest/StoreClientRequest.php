<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
            "message" => "Validation failed",
            "error" => $validator->errors(),
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
            'credit_limit' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric',
            'status' => 'required|in:positive,current,negative',
        ];
    }
}