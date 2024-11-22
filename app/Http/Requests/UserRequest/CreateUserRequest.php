<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255|unique:users,username|regex:/^[a-zA-Z0-9._-]+$/',
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20|regex:/^[0-9]+$/',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'El nombre de usuario es requerido',
            'username.string' => 'El nombre de usuario debe ser una cadena de texto',
            'username.max' => 'El nombre de usuario no debe exceder 255 caracteres',
            'username.unique' => 'Este nombre de usuario ya está en uso',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números y los caracteres . _ -',
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no debe exceder 255 caracteres',
            'lastname.required' => 'El apellido es requerido',
            'lastname.string' => 'El apellido debe ser una cadena de texto',
            'lastname.max' => 'El apellido no debe exceder 255 caracteres',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'Debe ser un correo electrónico válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'phone.max' => 'El teléfono no debe exceder 20 caracteres',
            'phone.regex' => 'El teléfono solo puede contener números',
        ];
    }
}